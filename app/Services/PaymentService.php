<?php

namespace App\Services;

use App\Events\TransactionOrdered;
use App\Services\GopayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Http};
use App\Models\{Log, PaymentMethod, Status, Transaction};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    public function listPaymentMethods()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->select('id', 'name', 'code', 'description', 'logo')
            ->get();

        return $paymentMethods;
    }

    public function setPaymentMethod(
        Request $request
    ) {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'transaction_id' => 'required|uuid|exists:transactions,id',
                'payment_method' => 'required|uuid|exists:payment_methods,id',
            ]);

            $transaction = Transaction::with([
                'transactionDetails',
                'status',
                'currency',
                'product'
            ])->find($request->transaction_id);
            $paymentMethod = PaymentMethod::find($request->payment_method);

            if (in_array($transaction->status, [Status::STATUS_COMPLETED, Status::STATUS_PAID, Status::STATUS_EXPIRED, Status::STATUS_ORDERED])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot change the payment method for this transaction',
                ], 400);
            }

            $transaction->paymentMethod()->associate($paymentMethod);
            $transaction->save();

            event(new TransactionOrdered($transaction));

            dd('Sabar');
        });
    }

    public function processPayment(
        Transaction $transaction,
        Request $request
    ) {
        return DB::transaction(function () use ($transaction, $request) {
            // Implement payment logic here

            switch ($request->payment_method) {
                case PaymentMethod::ID_GOPAY:
                    return $this->payWithGopay(
                        $transaction,
                        $request
                    );
                    break;
                case PaymentMethod::ID_BANK_TRANSFER:
                    break;
                case PaymentMethod::ID_MANDIRI_VA:
                    break;
                case PaymentMethod::ID_BCA_VA:
                    break;
                // Add other payment methods here
                default:
                    throw new \Exception('Unsupported payment method');
            }
        });
    }

    private function payWithGopay(
        Transaction $transaction,
        Request $request
    ) {
        $orderId = $transaction->order_id;
        $amount = $transaction->total_amount;
        $callbackUrl = route('midtrans.callback');

        $gopay = app(GopayPaymentService::class);

        $response = $gopay->charge($orderId, $amount, $callbackUrl);

        // ambil QR code (butuh GET + basic auth)
        $qrBase64 = null;
        $qrUrl = $response['actions'][0]['url'] ?? null;

        if ($qrUrl) {
            $midtransServerKey = config('midtrans.server_key');
            $qrResponse = Http::withBasicAuth($midtransServerKey, '')
                ->get($qrUrl);

            if ($qrResponse->successful()) {
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrResponse->body());
            }
        }

        // Set Payment Method
        $transaction->paymentMethod()->associate($gopay->getPaymentMethod());

        return [
            'order_id'   => $transaction->order_id,
            'status'     => $response['transaction_status'] ?? 'unknown',
            'gopay_url'  => $response['actions'][1]['url'] ?? null, // deeplink
            'qr_base64'  => $response['qr_base64'] ?? null, // untuk ditampilkan langsung di FE
        ];
    }

    public function handleCallbackGopay(Request $request)
    {
        $payload = $request->all();

        // Kamu bisa log dulu untuk debug
        Log::info('Midtrans callback:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $status  = $payload['transaction_status'] ?? null;

        $transaction = Transaction::with(
            [
                'transactionDetails',
                'user',
                'status'
            ]
        )
            ->where('id', $orderId)->first();

        if (!$transaction) {
            throw new NotFoundHttpException('Transaction not found');
        }

        // Update status transaction
        if ($transaction) {
            $transaction->status()->associate(Status::where('code', $status)->first());
            $transaction->save();
        }
    }

    public function handleNotification(array $notificationData)
    {
        // Implement notification handling logic here
    }
}

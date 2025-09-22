<?php

namespace App\Services;

use App\Events\TransactionOrdered;
use App\Events\TransactionPaid;
use App\Services\GopayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Http, Log};
use App\Models\{PaymentMethod, Status, Transaction};
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

            return $transaction;
        });
    }

    public function processPayment(
        Transaction $transaction,
        Request $request
    ) {
        return DB::transaction(function () use ($transaction, $request) {
            // Implement payment logic here

            $result = null;

            switch ($request->payment_method) {
                case PaymentMethod::ID_GOPAY:
                    $result = $this->payWithGopay(
                        $transaction,
                        $request
                    );
                    break;
                case PaymentMethod::ID_BANK_TRANSFER:
                    $result = $this->payWithBankTransfer(
                        $transaction,
                        $request
                    );
                    break;
                case PaymentMethod::ID_MANDIRI_VA:
                    break;
                case PaymentMethod::ID_BCA_VA:
                    break;
                // Add other payment methods here
                default:
                    throw new \Exception('Unsupported payment method');
            }

            return $result;
        });
    }

    // skema dengan deeplink
    private function payWithGopay(Transaction $transaction, Request $request)
    {
        $orderId = $transaction->id;
        $amount = $transaction->total_amount;
        $callbackUrl = route('midtrans.callback');

        $gopay = app(GopayPaymentService::class);
        $response = $gopay->charge($orderId, $amount, $callbackUrl);
        Log::channel('transaction')->info('Midtrans RAW charge response:', $response);

        if (!isset($response['status_code']) || $response['status_code'] != '201') {
            $errorMessage = $response['status_message'] ?? 'Failed to create Midtrans transaction.';
            throw new \Exception($errorMessage);
        }

        // =================================================================
        // !! PERUBAHAN UTAMA DI SINI !!
        // Kita hanya perlu mencari action dengan nama 'deeplink-redirect'
        // =================================================================
        $actions = collect($response['actions']);
        $deepLinkAction = $actions->firstWhere('name', 'deeplink-redirect');
        $deepLinkUrl = $deepLinkAction['url'] ?? null;

        if (!$deepLinkUrl) {
            throw new \Exception('GoPay deep link URL not found in Midtrans response.');
        }

        // Sisa kode yang rumit (request kedua, base64, sleep) DIHAPUS SEMUA.

        // Update paid_amount
        $transaction->paid_amount = $amount;
        $transaction->save();

        // Kembalikan URL deep link ke frontend
        return [
            'order_id'       => $transaction->id,
            'status'         => $response['transaction_status'] ?? 'pending',
            'deep_link_url'  => $deepLinkUrl, // <-- Kirim URL ini ke frontend
        ];
    }


    // Skema dengan QRcode
    // private function payWithGopay(Transaction $transaction, Request $request)
    // {
    //     $orderId = $transaction->id;
    //     $amount = $transaction->total_amount;
    //     $callbackUrl = route('midtrans.callback');

    //     $gopay = app(GopayPaymentService::class);
    //     $response = $gopay->charge($orderId, $amount, $callbackUrl);
    //     Log::channel('transaction')->info('Midtrans RAW charge response:', $response);

    //     if (!isset($response['status_code']) || $response['status_code'] != '201') {
    //         $errorMessage = $response['status_message'] ?? 'Failed to create Midtrans transaction.';
    //         throw new \Exception($errorMessage);
    //     }

    //     // =================================================================
    //     // !! PERUBAHAN UTAMA DI SINI !!
    //     // Kita tidak lagi menggunakan actions[0], tapi mencari berdasarkan nama.
    //     // =================================================================
    //     $actions = collect($response['actions']);
    //     $qrAction = $actions->firstWhere('name', 'generate-qr-code-v2');
    //     $qrUrl = $qrAction['url'] ?? null;
    //     // =================================================================

    //     $qrBase64 = null;
    //     if ($qrUrl) {
    //         $midtransServerKey = config('midtrans.server_key');

    //         // =================================================================
    //         // !! PERBAIKAN DI SINI !!
    //         // Tambahkan header Accept: image/png
    //         // =================================================================
    //         $qrResponse = Http::withBasicAuth($midtransServerKey, '')
    //             ->withHeaders(['Accept' => 'image/png']) // <-- TAMBAHKAN HEADER INI
    //             ->get($qrUrl);
    //         // =================================================================

    //         if ($qrResponse->successful()) {
    //             $qrBase64 = 'data:image/png;base64,' . base64_encode($qrResponse->body());
    //         } else {
    //             Log::channel('transaction')->error('Gagal mengambil gambar QR Code dari Midtrans.', [
    //                 'url' => $qrUrl,
    //                 'status_code' => $qrResponse->status(),
    //                 'body' => $qrResponse->json() ?? $qrResponse->body(),
    //             ]);
    //         }
    //     }

    //     $transaction->paid_amount = $amount;
    //     $transaction->save();

    //     return [
    //         'order_id'   => $transaction->id,
    //         'status'     => $response['transaction_status'] ?? 'pending',
    //         'qr_base64'  => $qrBase64,
    //     ];
    // }

    public function handleCallbackGopay(Request $request)
    {
        $payload = $request->all();

        // Kamu bisa log dulu untuk debug
        Log::channel('transaction')->info('Midtrans callback:', $payload);

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

            event(new TransactionPaid($transaction));

            return response()->json(['message' => 'Transaction updated']);
        }
    }

    public function payWithBankTransfer(
        Transaction $transaction,
        Request $request
    ) {
        // Implement bank transfer payment logic here
        $file = $request->file('proof_of_payment');

        // save to cloud storage
        $fileUploadService = app(FileUploadService::class);
        $fileDir = 'proof_of_payments';
        $result = $fileUploadService->uploadFile($file->getPathname(), $fileDir);

        // update paid_amount
        $transaction->paid_amount = $transaction->total_amount;
        $transaction->save();
        return [];
    }

    public function handleNotification(array $notificationData)
    {
        // Implement notification handling logic here
    }
}

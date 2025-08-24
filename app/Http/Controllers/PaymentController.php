<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Services\ErrorHandler;
use App\Services\GopayPaymentService;
use App\Services\MidtransService;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class PaymentController extends Controller
{
    use ValidatesRequests;

    private $errorHandler, $midtransService, $midtransServerKey;
    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->midtransService = new MidtransService;
        $this->midtransServerKey = config('midtrans.server_key');
    }

    public function list(Request $request)
    {
        try {
            $paymentMethods = PaymentMethod::where('is_active', true)
                ->select('id', 'name', 'description', 'logo')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $paymentMethods,
            ]);
        } catch (Throwable $error) {
            return $this->errorHandler->handle($error);
        }
    }

    public function handleNotification(Request $request)
    {
        $notification = new Notification();
        $transaction_status = $notification->transaction_status;
        $order_id = $notification->order_id;

        $statuses = Status::get();

        if ($transaction_status == 'settlement') {
            $statusPaid = $statuses->where('code', 'paid')->first();
            Transaction::where('id', $order_id)->update(['status' => $statusPaid->id]);
        } elseif ($transaction_status == 'pending') {
            $statusPending = $statuses->where('code', 'pending')->first();
            Transaction::where('id', $order_id)->update(['status' => $statusPending->id]);
        } elseif ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
            $statusCancel = $statuses->where('code', 'cancel')->first();
            Transaction::where('id', $order_id)->update(['status' => $statusCancel->id]);
        }

        return response()->json(['message' => 'Notification processed']);
    }

    public function payWithGopay(Transaction $transaction, Request $request, GopayPaymentService $gopay)
    {
        try {
            $orderId = $transaction->id;
            $amount = $transaction->total_amount;
            $callbackUrl = route('midtrans.callback');

            $response = $gopay->charge($orderId, $amount, $callbackUrl);

            // ambil QR code (butuh GET + basic auth)
            $qrBase64 = null;
            $qrUrl = $response['actions'][0]['url'] ?? null;

            if ($qrUrl) {
                $qrResponse = Http::withBasicAuth($this->midtransServerKey, '')
                    ->get($qrUrl);

                if ($qrResponse->successful()) {
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrResponse->body());
                }
            }

            // Set Payment Method
            $transaction->paymentMethod()->associate($gopay->getPaymentMethod());

            return response()->json([
                'order_id'   => $orderId,
                'status'     => $response['transaction_status'] ?? 'unknown',
                'gopay_url'  => $response['actions'][1]['url'] ?? null, // deeplink
                'qr_base64'  => $qrBase64, // untuk ditampilkan langsung di FE
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function handleCallbackGopay(Request $request)
    {
        try {
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

            return response()->json(['message' => 'Callback received'], 200);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

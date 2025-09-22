<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Models\{Transaction};
use App\Services\{ErrorHandler, MidtransService};
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    use ValidatesRequests;

    private $errorHandler, $midtransService, $paymentService;
    public function __construct(
        ErrorHandler $errorHandler,
        MidtransService $midtransService,
        PaymentService $paymentService
    ) {
        $this->errorHandler = $errorHandler;
        $this->midtransService = $midtransService;
        $this->paymentService = $paymentService;
    }

    public function list(Request $request)
    {
        try {
            $paymentMethods = $this->paymentService->listPaymentMethods();

            return response()->json([
                'status' => 'success',
                'data' => $paymentMethods,
            ]);
        } catch (Throwable $error) {
            return $this->errorHandler->handle($error);
        }
    }

    public function setPaymentMethod(Request $request)
    {
        try {
            $transaction = $this->paymentService->setPaymentMethod($request);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment method set successfully',
                'data' => $transaction,
            ]);
        } catch (Throwable $error) {
            return $this->errorHandler->handle($error);
        }
    }

    public function pay(
        Transaction $transaction,
        Request $request,
    ) {
        try {
            $result = $this->paymentService->processPayment($transaction, $request);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    // public function handleNotification(Request $request)
    // {
    //     $notification = new Notification();
    //     $transaction_status = $notification->transaction_status;
    //     $order_id = $notification->order_id;

    //     $statuses = Status::get();

    //     if ($transaction_status == 'settlement') {
    //         $statusPaid = $statuses->where('code', 'paid')->first();
    //         Transaction::where('id', $order_id)->update(['status' => $statusPaid->id]);
    //     } elseif ($transaction_status == 'pending') {
    //         $statusPending = $statuses->where('code', 'pending')->first();
    //         Transaction::where('id', $order_id)->update(['status' => $statusPending->id]);
    //     } elseif ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
    //         $statusCancel = $statuses->where('code', 'cancel')->first();
    //         Transaction::where('id', $order_id)->update(['status' => $statusCancel->id]);
    //     }

    //     return response()->json(['message' => 'Notification processed']);
    // }

    // public function payWithGopay(Transaction $transaction, Request $request)
    // {
    //     try {
    //         $response = $this->paymentService->processPayment($transaction, $request);

    //         return response()->json([
    //             'order_id'   => $transaction->order_id,
    //             'status'     => $response['transaction_status'] ?? 'unknown',
    //             'gopay_url'  => $response['actions'][1]['url'] ?? null, // deeplink
    //             'qr_base64'  => $response['qr_base64'] ?? null, // untuk ditampilkan langsung di FE
    //         ]);
    //     } catch (Throwable $e) {
    //         return $this->errorHandler->handle($e);
    //     }
    // }

    public function handleCallbackGopay(Request $request)
    {
        try {
            $result = $this->paymentService->handleCallbackGopay($request);

            return response()->json(['message' => 'Callback received'], 200);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function getTransactionStatus(Transaction $transaction)
    {
        // Pastikan user yang meminta adalah pemilik transaksi (opsional tapi sangat direkomendasikan)
        // if ($transaction->user_id !== auth()->id()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        return response()->json([
            'status' => 'success',
            'data' => [
                'transaction_id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'status' => $transaction->status->name, // Asumsi ada relasi 'status' dan kolom 'name'
                'status_code' => $transaction->status->code, // Asumsi ada kolom 'code' (misal: 'settlement')
            ],
        ]);
    }
}

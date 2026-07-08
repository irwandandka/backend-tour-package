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
        try {
            if ($transaction->user_id !== auth('api')->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->id,
                    'status' => $transaction->status->name,
                    'status_code' => $transaction->status->code,
                ],
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

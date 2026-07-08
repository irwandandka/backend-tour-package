<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\SetPaymentMethodRequest;
use App\Models\Transaction;
use App\Services\ErrorHandler;
use App\Services\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    private $errorHandler;

    private $paymentService;

    public function __construct(
        ErrorHandler $errorHandler,
        PaymentService $paymentService
    ) {
        $this->errorHandler = $errorHandler;
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

    public function setPaymentMethod(SetPaymentMethodRequest $request)
    {
        try {
            $transaction = Transaction::findOrFail($request->validated('transaction_id'));
            $this->authorize('pay', $transaction);

            $transaction = $this->paymentService->setPaymentMethod($transaction, $request);

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
            $this->authorize('pay', $transaction);

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
            $this->authorize('view', $transaction);

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

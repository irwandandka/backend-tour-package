<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Status;
use App\Models\Transaction;
use App\Services\ErrorHandler;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Midtrans\Snap;
use Throwable;

class PaymentController extends Controller
{
    use ValidatesRequests;

    private $errorHandler;
    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
    }

    public function payment(Request $request, $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->first();

            if (!$transaction) {
                throw new Exception('Transaction not found', 404);
            }

            $validate = $this->validate($request, [
                'payment_method' => 'required|exists:payment_methods,id',
            ]);

            $transaction_details = [
                'order_id' => $transaction->id,
                'gross_amount' => $transaction->total_amount,
            ];

            $customer_details = [
                'first_name' => $transaction->first_name,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ];

            $params = [
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
            ];

            // make payment record
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'payment_method' => $validate['payment_method'],
                'amount' => $transaction->total_amount,
                'currency' => $transaction->currency,
            ]);

            dd('Sabar ya, ini masih contoh');

            $snapToken = Snap::getSnapToken($params);

            // Logic to pay a booking
            return response()->json(['token' => $snapToken]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
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
}

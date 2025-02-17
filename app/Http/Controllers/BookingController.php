<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Transaction;
use App\Services\BookingService;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use ValidatesRequests;

    private $errorHandler;
    private $bookingService;

    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->bookingService = new BookingService;
    }

    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            $validate = $request->validate([
                'currency' => 'required|in:USD,IDR',
                'product_id' => 'required|exists:products,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'quantity_adult' => 'required|integer',
                'quantity_child' => 'required|integer',
                'quantity_infant' => 'required|integer',
                'quantity_senior' => 'required|integer',
            ]);

            $result = DB::transaction(function () use ($validate, $user) {
                $transaction = $this->bookingService->createTransaction($validate, $user);

                return $transaction;
            });

            // Logic to book a product
            return response()->json([
                'status' => 'success',
                'message' => 'Booking success',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::with(
                [
                    'product',
                    'status',
                    'transactionDetails',
                ]
            )
                ->where('id', $id)
                ->first();

            $transactionDetail = $transaction->transactionDetails->first();

            $data = [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'status' => $transaction->status->name,
                'product' => $transaction->product->name,
                'quantity' => $transaction->quantity,
                'sales_total' => $transaction->sales_total,
                'sales_total_base' => $transaction->sales_total_base,
                'booking_date' => Carbon::parse($transaction->booking_date)->format('l, jS F Y'),
                'notes' => $transaction->notes,
                'details' => [
                    'quantity_adult' => $transactionDetail->quantity_adult,
                    'quantity_child' => $transactionDetail->quantity_child,
                    'quantity_infant' => $transactionDetail->quantity_infant,
                    'quantity_senior' => $transactionDetail->quantity_senior,
                    'sales_adult' => $transactionDetail->sales_adult,
                    'sales_child' => $transactionDetail->sales_child,
                    'sales_infant' => $transactionDetail->sales_infant,
                    'sales_senior' => $transactionDetail->sales_senior,
                ],
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction detail',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function cancel($id)
    {
        try {
            $transaction = Transaction::find($id);

            $statusCancel = Status::where('code', 'cancel')->first();

            $transaction->status_id = 3;
            $transaction->save();

            // Logic to cancel a booking
            return response()->json([
                'status' => 'success',
                'message' => 'Booking canceled',
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'required|string',
                'postal_code' => 'required|string',
                'passengers' => 'required|array',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $transaction = Transaction::find($id);

                $transaction->customer_name = $validated['name'];
                $transaction->customer_email = $validated['email'];
                $transaction->customer_phone = $validated['phone'];
                $transaction->address = $validated['address'];
                $transaction->postal_code = $validated['postal_code'];
                $transaction->save();

                $paramPassengers = $validated['passengers'];

                $paramPassengers = array_map(function ($passenger) use ($transaction) {
                    return array_merge($passenger, [
                        'id' => Str::uuid(),
                        'created_at' => Carbon::now(),
                        'transaction_id' => $transaction->id
                    ]);
                }, $paramPassengers);

                Passenger::insert($paramPassengers);

                return $transaction->only(
                    ['id', 'code', 'quantity', 'total_amount', 'booking_date', 'notes']
                );
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Passenger detail updated',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

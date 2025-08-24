<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Status;
use App\Models\Transaction;
use App\Services\BookingService;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

            $validated = $request->validate([
                'currency' => 'required',
                'product_id' => 'required|exists:products,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'product_details' => 'required|array|min:1',
                'product_details.*.product_detail' => 'required|exists:product_details,id',
                'product_details.*.quantity' => 'required|integer|min:1',
                'product_details.*.quantity_adult' => 'required|integer|min:0',
                'product_details.*.quantity_child' => 'required|integer|min:0',
                'product_details.*.quantity_infant' => 'required|integer|min:0',
                'product_details.*.quantity_senior' => 'required|integer|min:0',
            ]);

            $result = DB::transaction(function () use ($validated, $user) {
                $transaction = $this->bookingService->createTransaction($validated, $user);

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
                    "product",
                    "status",
                    "transactionDetails",
                    "transactionDetails.product",
                    "transactionDetails.productDetail",
                ]
            )
                ->where('id', $id)
                ->first();

            $data = [
                "id" => $transaction->id,
                "code" => $transaction->code,
                "status" => $transaction->status->name,
                "product" => $transaction->product->name,
                "quantity" => $transaction->quantity,
                "total_amount" => $transaction->total_amount,
                "total_amount_base" => $transaction->total_amount_base,
                "booking_date" => Carbon::parse($transaction->booking_date)->format("l, jS F Y"),
                "notes" => $transaction->notes,
                "customer_name" => $transaction->customer_name,
                "customer_email" => $transaction->customer_email,
                "from_date" => Carbon::parse($transaction->date_from)->format("l, jS F Y"),
                "to_date" => Carbon::parse($transaction->date_to)->format("l, jS F Y"),
                "transaction_details" => $transaction
                    ->transactionDetails
                    ->map(function ($detail) {
                        return [
                            "product_detail_name" => $detail->productDetail->name_en,
                            "product_detail_image" => $detail->productDetail->activity_image,
                            "quantity_adult" => $detail->quantity_adult,
                            "quantity_child" => $detail->quantity_child,
                            "quantity_infant" => $detail->quantity_infant,
                            "quantity_senior" => $detail->quantity_senior,
                            "sales_adult" => $detail->sales_adult,
                            "sales_child" => $detail->sales_child,
                            "sales_infant" => $detail->sales_infant,
                            "sales_senior" => $detail->sales_senior,
                        ];
                    }),
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
                'passengers.*.first_name' => 'required|string',
                'passengers.*.last_name' => 'required|string',
                'passengers.*.title' => 'required|string',
                'passengers.*.nationality' => 'string|nullable',
                'passengers.*.passport_number' => 'string|nullable|unique:passengers,passport_number',
                'passengers.*.passport_expiry_date' => 'string|nullable|date_format:Y-m-d',
                'passengers.*.passport_issue_date' => 'string|nullable|date_format:Y-m-d',
                'passengers.*.passport_issue_country' => 'string|nullable',
                'passengers.*.birth_place' => 'string|nullable',
                'passengers.*.birth_date' => 'string|nullable|date_format:Y-m-d',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $transaction = Transaction::with([
                    'transactionDetails',
                    'passengers',
                ])
                    ->where('id', $id)
                    ->first();

                if ($transaction->passengers->isEmpty()) {
                    $totalPax = $transaction->transactionDetails->sum(function ($detail) {
                        return $detail->quantity_adult + $detail->quantity_child + $detail->quantity_infant;
                    });

                    if (count($validated['passengers']) < $totalPax) {
                        throw new Exception('Total passengers must not be less than ' . $totalPax);
                    }

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
                }

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

    public function history(Request $request)
    {
        try {
            $user = Auth::user();

            $userData = User::with(
                [
                    "transactions",
                    "transactions.product",
                    "transactions.status",
                    "transactions.transactionDetails",
                    "transactions.transactionDetails.product",
                    "transactions.transactionDetails.productDetail",
                ]
            )
                ->where('id', $user->id)
                ->first();

            $bookings = $userData
                ->transactions
                ->map(function ($transaction) {
                    return [
                        "id" => $transaction->id,
                        "code" => $transaction->code,
                        "status" => $transaction->status->name,
                        "product" => $transaction->product->name,
                        "quantity" => $transaction->quantity,
                        "total_amount" => $transaction->total_amount,
                        "total_amount_base" => $transaction->total_amount_base,
                        "booking_date" => Carbon::parse($transaction->booking_date)->format("l, jS F Y"),
                        "notes" => $transaction->notes,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $bookings
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

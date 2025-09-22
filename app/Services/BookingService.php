<?php

namespace App\Services;

use App\Models\{Currency, Passenger, Product, Status, Transaction, TransactionDetail, User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class BookingService
{
    protected $pricingService;

    public function __construct()
    {
        $this->pricingService = new PricingService;
    }

    public function createTransaction(Request $request)
    {
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

        $statusEntry = Status::where('code', 'entry')->first();
        $bookingCode = generateTransactionCode();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'date_from' => $validated['date_from'],
            'date_to' => $validated['date_to'],
            'status_id' => $statusEntry->id,
            'code' => $bookingCode,
            'booking_date' => now(),
            'notes' => 'Booking a product',
        ]);

        $product = Product::with(
            [
                'product_details',
                'product_details.product_prices',
                'purchase_currency',
                'sales_currency',
            ]
        )
            ->where('id', $validated['product_id'])
            ->first();

        $currencies = Currency::get();

        $salesTotal = $salesTotalBase = 0;

        collect($validated['product_details'])
            ->map(function ($product_detail) use (
                $validated,
                $product,
                $currencies,
                $transaction,
                $user,
                &$salesTotal,
                &$salesTotalBase
            ) {
                $detail = $this->pricingService->calculatePricing(
                    $product,
                    $currencies,
                    $product_detail,
                    $validated
                );

                $detail['transaction_id'] = $transaction->id;
                $detail['user_id'] = $user->id;

                $salesTotal += $detail['sales_total'];
                $salesTotalBase += $detail['sales_total_base'];

                return $detail;
            })
            ->each(function ($detail) {
                TransactionDetail::create($detail);
            });

        $transaction->total_amount = $salesTotal;
        $transaction->total_amount_base = $salesTotalBase;
        $transaction->currency_id = $currencies->where('code', $validated['currency'])->first()->id;
        $transaction->save();

        return $transaction;
    }

    public function getTransaction(
        string $id
    ) {
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

        return $transaction;
    }

    public function cancelBooking(string $id)
    {
        $transaction = Transaction::find($id);

        $transaction->status_id = 3;
        $transaction->save();

        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);

        return $transaction;
    }

    public function updateBooking(string $id, Request $request)
    {
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

        return DB::transaction(function () use ($validated, $id) {
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
                    throw new ValidationException('Total passengers must not be less than ' . $totalPax);
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

            return $transaction;
        });
        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);
    }

    public function sendBookingEmail($email, $bookingDetails)
    {
        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);
    }

    public function getHistory(
        Request $request
    ) {
        $user = auth('api')->user();

        $userData = User::with(
            [
                "transactions" => function ($query) use ($request) {
                    if ($request->has('status')) {
                        $query->whereHas('status', function ($q) use ($request) {
                            $q->where('code', $request->get('status'));
                        });
                    }
                },
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
                return (object) [
                    "id" => $transaction->id,
                    "code" => $transaction->code,
                    "status" => $transaction->status->name,
                    "product" => $transaction->product->name,
                    "image" => $transaction->product->thumbnail_image,
                    "total_amount" => $transaction->total_amount,
                    "total_amount_base" => $transaction->total_amount_base,
                    "booking_date" => Carbon::parse($transaction->booking_date)->format("l, jS F Y"),
                    "notes" => $transaction->notes,
                ];
            });
        return $bookings;
    }
}

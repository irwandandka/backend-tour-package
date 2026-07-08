<?php

namespace App\Services;

use App\Models\{Currency, Passenger, Review, Product, Status, Transaction, TransactionDetail, User};
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function createTransaction(Request $request)
    {
        $user = auth('api')->user();
        $validated = $request->validated();

        $statusEntry = Status::where('code', 'entry')->first();
        $bookingCode = generateTransactionCode();

        $currencies = Currency::get();
        $currency = $currencies->where('code', $validated['currency'])->first();

        if (!$currency) {
            throw new NotFoundHttpException('Currency not found');
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'date_from' => $validated['date_from'],
            'date_to' => $validated['date_to'],
            'status_id' => $statusEntry->id,
            'currency_id' => $currency->id,
            'code' => $bookingCode,
            'booking_date' => now(),
            'notes' => 'Booking a product',
        ]);

        if (!$transaction) {
            throw new Exception('Failed to create transaction');
        }

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
        $transaction->save();

        return $transaction;
    }

    public function getTransaction(Transaction $transaction)
    {
        return $transaction->load(
            [
                "product",
                "status",
                "transactionDetails",
                "transactionDetails.product",
                "transactionDetails.productDetail",
                "eticket",
            ]
        );
    }

    public function cancelBooking(Transaction $transaction)
    {
        $transaction->status_id = Status::STATUS_CANCELLED;
        $transaction->save();

        return $transaction;
    }

    public function updateBooking(Transaction $transaction, Request $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $transaction) {
            $transaction->load(['transactionDetails', 'passengers']);

            if ($transaction->passengers->isEmpty()) {
                $totalPax = $transaction->transactionDetails->sum(function ($detail) {
                    return $detail->quantity_adult + $detail->quantity_child + $detail->quantity_infant;
                });

                if (count($validated['passengers']) < $totalPax) {
                    throw ValidationException::withMessages([
                        'passengers' => 'Total passengers must not be less than ' . $totalPax,
                    ]);
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
            ->sortByDesc('created_at')
            ->map(function ($transaction) {
                return (object) [
                    "id" => $transaction->id,
                    "code" => $transaction->code,
                    "status" => $transaction->status->name,
                    "product" => $transaction->product->name,
                    "slug" => $transaction->product->slug,
                    "image" => $transaction->product->thumbnail_image,
                    "total_amount" => $transaction->total_amount,
                    "total_amount_base" => $transaction->total_amount_base,
                    "booking_date" => Carbon::parse($transaction->booking_date)->format("l, jS F Y"),
                    "notes" => $transaction->notes,
                ];
            });
        return $bookings;
    }

    public function submitReview(Request $request, Transaction $transaction)
    {
        $validated = $request->validated();

        Review::create([
            'user_id' => $transaction->user_id,
            'product_id' => $transaction->product_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'review_date' => now(),
        ]);

        return $transaction;
    }
}

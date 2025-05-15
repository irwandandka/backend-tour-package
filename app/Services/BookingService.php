<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;

class BookingService
{
    protected $pricingService;

    public function __construct()
    {
        $this->pricingService = new PricingService;
    }

    public function createTransaction($data, $user)
    {
        // create logic to create a transaction and its transaction details

        $statusEntry = Status::where('code', 'entry')->first();
        $bookingCode = generateTransactionCode();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
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
            ->where('id', $data['product_id'])
            ->first();

        $currencies = Currency::get();

        $salesTotal = $salesTotalBase = 0;

        collect($data['product_details'])
            ->map(function ($product_detail) use (
                $data,
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
                    $data
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

    public function cancelBooking($email, $bookingDetails)
    {
        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);
    }

    public function updateBooking($email, $bookingDetails)
    {
        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);
    }

    public function sendBookingEmail($email, $bookingDetails)
    {
        // $job = new SendBookingEmail($email, $bookingDetails);
        // dispatch($job);
    }
}

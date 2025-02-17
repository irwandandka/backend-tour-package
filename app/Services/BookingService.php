<?php

namespace App\Services;

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

        $detail = $this->pricingService->calculatePricing($data['product_id'], $data['currency'], $data);

        $detail['transaction_id'] = $transaction->id;
        $detail['product_id'] = $transaction->product_id;
        $detail['user_id'] = $user->id;
        $detail['date_from'] = Carbon::parse($data['date_from']);
        $detail['date_to'] = Carbon::parse($data['date_to']);
        $detail['quantity'] = 1;

        $transactionDetail = TransactionDetail::create($detail);

        $transaction->total_amount = $transactionDetail->sales_total;
        $transaction->total_amount_base = $transactionDetail->sales_total_base;
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

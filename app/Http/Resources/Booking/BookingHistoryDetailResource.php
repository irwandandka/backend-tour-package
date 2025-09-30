<?php

namespace App\Http\Resources\Booking;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingHistoryDetailResource extends JsonResource
{
    // Resource implementation here

    public function toArray(
        $request
    ): array {
        $transaction = $this->resource;

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
            "customer_name" => $transaction->customer_name,
            "customer_email" => $transaction->customer_email,
            'payment_method' => $transaction->paymentMethod ? $transaction->paymentMethod->name : null,
            "from_date" => Carbon::parse($transaction->date_from)->format("l, jS F Y"),
            "to_date" => Carbon::parse($transaction->date_to)->format("l, jS F Y"),
            'eticket' => $transaction->eticket,
            "transaction_details" => $transaction->transactionDetails
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
    }
}

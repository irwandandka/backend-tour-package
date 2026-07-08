<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}

<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}

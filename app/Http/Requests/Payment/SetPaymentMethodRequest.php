<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class SetPaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|uuid|exists:transactions,id',
            'payment_method' => 'required|uuid|exists:payment_methods,id',
        ];
    }
}

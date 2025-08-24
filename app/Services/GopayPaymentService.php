<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\PaymentMethod;

class GopayPaymentService
{
    private $serverKey, $endpoint;
    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->endpoint = config('midtrans.endpoint_sandbox');
    }

    public function charge(string $orderId, int $amount, string $callbackUrl)
    {
        $body = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => $callbackUrl,
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->post($this->endpoint . 'charge', $body);

        return $response->json();
    }

    public function getPaymentMethod()
    {
        return PaymentMethod::where('code', 'gopay')->first();
    }
}

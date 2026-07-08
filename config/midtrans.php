<?php

$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

return [
    'server_key' => $isProduction ? env('MIDTRANS_SERVER_KEY_PRODUCTION') : env('MIDTRANS_SERVER_KEY'),
    'client_key' => $isProduction ? env('MIDTRANS_CLIENT_KEY_PRODUCTION') : env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => $isProduction ? env('MIDTRANS_MERCHANT_ID_PRODUCTION') : env('MIDTRANS_MERCHANT_ID'),
    'is_production' => $isProduction,
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'is_sanitize' => env('MIDTRANS_IS_SANITIZE', true),
    'endpoint_sandbox' => env('MIDTRANS_ENDPOINT_SANDBOX'),
    'endpoint_production' => env('MIDTRANS_ENDPOINT_PRODUCTION'),
];

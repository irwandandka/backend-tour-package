<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;
use Throwable;
use Illuminate\Http\Request;

class MidtransService
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitize');
        Config::$is3ds = config('midtrans.is_3ds');

        $this->errorHandler = $errorHandler;
    }

    public function createTransaction(array $params)
    {
        try {
            return Snap::createTransaction($params);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

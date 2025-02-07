<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ErrorHandler;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class CrawlingController extends Controller
{
    private $errorHandler;
    private $logService;
    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->logService = new LogService;
    }

    public function getCurrencyRates()
    {
        try {
            set_time_limit(360); // 6 minutes

            // create logic to get currency rates based on SGD
            $url = env('OPEN_EXCHANGE_RATES_URL');
            $appId = env('OPEN_EXCHANGE_RATES_APP_ID');

            $response = Http::get($url, [
                'app_id' => $appId,
                'base' => 'USD',
            ]);

            if ($response->failed()) {
                return response()->json('Failed to get currency rates', 500);
            }

            $data = $response->json();
            $usdToSgd = $data['rates']['SGD'];
            $timestamp = Carbon::now();

            DB::transaction(function () use ($data, $usdToSgd, $timestamp) {
                foreach ($data['rates'] as $currency => $rate) {
                    if ($currency !== 'SGD') {
                        $sgdToTarget = $rate / $usdToSgd;

                        $currencySGD = Currency::where('code', 'SGD')->first();
                        $targetCurrency = Currency::where('code', $currency)->first();

                        // Simpan kurs ke database
                        ExchangeRate::updateOrCreate(
                            [
                                'base_currency_id' => $currencySGD->id,
                                'target_currency_id' => $targetCurrency->id,
                                'timestamp' => $timestamp,
                            ],
                            [
                                'exchange_rate' => $sgdToTarget,
                            ]
                        );
                    }
                }
            });

            $this->logService->info('crawling', 'Currency rates updated');

            return response()->json('Currency rates updated');
        } catch (Throwable $error) {
            return $this->errorHandler->handle($error);
        }
    }
}

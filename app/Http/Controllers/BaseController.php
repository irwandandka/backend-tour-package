<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;
use App\Models\Currency;

class BaseController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function languages(Request $request)
    {
        try {
            $languages = [
                [
                    'name' => 'English',
                    'code' => 'en'
                ],
                [
                    'name' => 'Indonesia',
                    'code' => 'id'
                ],
            ];

            return response()->json([
                'status' => 'success',
                'data' => $languages
            ]);
        } catch (Exception $e) {
            return $this->errorHandler->handleError($e);
        }
    }

    public function currencies(Request $request)
    {
        try {
            $currencies = Currency::where('is_active', true)
                ->get()
                ->map(function ($currency) {
                    return [
                        'id' => $currency->id,
                        'name' => $currency->name,
                        'code' => $currency->code,
                        'symbol' => $currency->symbol,
                        'exchange_rate' => $currency->exchange_rate,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $currencies,
            ]);
        } catch (Exception $e) {
            return $this->errorHandler->handleError($e);
        }
    }
}

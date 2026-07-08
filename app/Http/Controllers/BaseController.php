<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Throwable;

class BaseController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @see SwaggerInfo::languages()
     */
    public function languages(Request $request)
    {
        try {
            $languages = [
                [
                    'name' => 'English',
                    'code' => 'en',
                    'logo' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/america-flag.webp',
                ],
                [
                    'name' => 'Indonesia',
                    'code' => 'id',
                    'logo' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/indonesia-flag.webp',
                ],
                [
                    'name' => 'Chinese Simplified',
                    'code' => 'zh-CN',
                    'logo' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/china-flag.webp',
                ],
                [
                    'name' => 'Thailand',
                    'code' => 'th',
                    'logo' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/thailand-flag.webp',
                ],
            ];

            return response()->json([
                'status' => 'success',
                'data' => $languages,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::currencies()
     */
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
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Throwable;

class RegionController extends Controller
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @see SwaggerInfo::regions()
     */
    public function list(Request $request)
    {
        try {
            $regions = Region::with(
                [
                    'countries',
                ]
            )
                ->whereIn('code', ['as', 'eu', 'sea', 'me'])
                ->get()
                ->map(function ($region) {
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'code' => $region->code,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $regions,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::regionShow()
     */
    public function show(Region $region)
    {
        try {
            $region->load('countries');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $region->id,
                    'name' => $region->name,
                    'code' => $region->code,
                    'countries' => $region->countries->map(function ($country) {
                        return [
                            'id' => $country->id,
                            'name' => $country->name,
                            'iso_code' => $country->iso_code,
                            'phone_code' => $country->phone_code,
                        ];
                    }),
                ],
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

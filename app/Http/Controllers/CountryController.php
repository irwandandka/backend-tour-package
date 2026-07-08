<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Throwable;

class CountryController extends Controller
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function list(Request $request)
    {
        try {
            $countries = Country::with(
                [
                    'cities',
                ]
            )
                ->get()
                ->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'iso_code' => $country->iso_code,
                        'phone_code' => $country->phone_code,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $countries,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(Country $country)
    {
        try {
            $country->load('cities');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'iso_code' => $country->iso_code,
                    'phone_code' => $country->phone_code,
                    'cities' => $country->cities->map(function ($city) {
                        return [
                            'id' => $city->id,
                            'name' => $city->name,
                            'latitude' => $city->latitude,
                            'longitude' => $city->longitude,
                        ];
                    }),
                ],
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

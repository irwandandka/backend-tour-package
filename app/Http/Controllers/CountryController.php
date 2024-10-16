<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Throwable;
use App\Services\ErrorHandler;

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
            $regions = Country::with(['cities'])->get();

            return ApiResponseClass::sendResponse(CountryResource::collection($regions), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(Country $country)
    {
        try {
            $country->load('cities');
            return ApiResponseClass::sendResponse(new CountryResource($country), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

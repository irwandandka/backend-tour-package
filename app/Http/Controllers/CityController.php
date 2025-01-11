<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Throwable;
use App\Services\ErrorHandler;

class CityController extends Controller
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function list(Request $request)
    {
        try {
            $cities = City::with(['country', 'region'])->get();

            return ApiResponseClass::sendResponse(CityResource::collection($cities), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(City $city)
    {
        try {
            $city->load('country', 'region');

            return ApiResponseClass::sendResponse(new CityResource($city), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function delete(City $city)
    {
        try {
            $city->delete();

            return ApiResponseClass::sendResponse([], 'Deleted Successfully!');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function getDeleted()
    {
        try {
            $deletedCities = City::onlyTrashed()->get();

            return ApiResponseClass::sendResponse(CityResource::collection($deletedCities), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

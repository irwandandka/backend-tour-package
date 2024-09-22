<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function list(Request $request)
    {
        $cities = City::with(['country', 'region'])->get();

        return ApiResponseClass::sendResponse(CityResource::collection($cities), '');
    }

    public function show(City $city)
    {
        $city->load('country', 'region');

        return ApiResponseClass::sendResponse(new CityResource($city), '');
    }
}

<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function list(Request $request)
    {
        $regions = Country::with(['cities'])->get();

        return ApiResponseClass::sendResponse(CountryResource::collection($regions), '');
    }

    public function show(Country $country)
    {
        $country->load('cities');
        return ApiResponseClass::sendResponse(new CountryResource($country), '');
    }
}

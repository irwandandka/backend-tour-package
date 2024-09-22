<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Resources\RegionResource;

class RegionController extends Controller
{
    public function list(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        $regions = Region::with(['countries'])->get();

        return ApiResponseClass::sendResponse(RegionResource::collection($regions), '');
    }

    public function show(Region $region)
    {
        return ApiResponseClass::sendResponse(new RegionResource($region), '');
    }
}

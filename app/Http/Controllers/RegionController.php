<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Resources\RegionResource;
use App\Services\ErrorHandler;
use Throwable;

class RegionController extends Controller
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function list(Request $request)
    {
        try {
            $userAgent = $request->header('User-Agent');

            $regions = Region::with(['countries'])->get();

            return ApiResponseClass::sendResponse(RegionResource::collection($regions), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(Region $region)
    {
        try {
            $region->load('countries');
            return ApiResponseClass::sendResponse(new RegionResource($region), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

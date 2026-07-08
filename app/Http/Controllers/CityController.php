<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Throwable;

class CityController extends Controller
{
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @see SwaggerInfo::cities()
     */
    public function list(Request $request)
    {
        try {
            $validate = $request->validate([
                'country' => 'sometimes|exists:countries,id',
                'region' => 'sometimes|exists:regions,id',
            ]);

            $cities = City::with(
                [
                    'country',
                    'region',
                ]
            )
                ->when(isset($validate['country']), function ($query) use ($validate) {
                    return $query->where('country_id', $validate['country']);
                })
                ->when(isset($validate['region']), function ($query) use ($validate) {
                    return $query->where('region_id', $validate['region']);
                })
                ->get()
                ->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'country' => $city->country->name,
                        'region' => $city->region->name,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $cities,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::showCity()
     */
    public function show(City $city)
    {
        try {
            $city->load('country', 'region');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => $city->country->name,
                    'region' => $city->region->name,
                ],
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::deleteCity()
     */
    public function delete(City $city)
    {
        try {
            $city->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'City deleted successfully',
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::getDeletedCities()
     */
    public function getDeleted()
    {
        try {
            $deletedCities = City::onlyTrashed()->get();
            $deletedCities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => $city->country->name,
                    'region' => $city->region->name,
                    'deleted_at' => $city->deleted_at->date_format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $deletedCities,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

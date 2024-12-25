<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\Region;
use Exception;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;

class SearchController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function globalSearch(Request $request)
    {
        try {
            $query = $request->input('query');

            // Search for every models
            $products = Product::search($query)->get()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'type' => 'Product',
                ];
            });

            $cities = City::search($query)->get()->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'type' => 'City'
                ];
            });

            $countries = Country::search($query)->get()->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'type' => 'Country'
                ];
            });

            $regions = Region::search($query)->get()->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'type' => 'Region'
                ];
            });

            $searchResult = $searchData = [];
            foreach ($products as $product) {
                $searchData[] = [];
            }

            $searchResult = $products
                ->merge($cities)
                ->merge($countries)
                ->merge($regions);

            return response()->json($searchResult);
        } catch (Exception $e) {
            return $this->errorHandler->handleError($e);
        }
    }
}

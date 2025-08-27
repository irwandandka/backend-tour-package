<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\Region;
use Throwable;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;
use Illuminate\Pagination\LengthAwarePaginator;

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
            $validated = $request->validate([
                'query' => 'required|string|min:1',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:50',
            ]);

            // Search for every models
            $products = Product::search($validated['query'])->get()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'type' => 'Product',
                ];
            });

            $cities = City::search($validated['query'])->get()->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'type' => 'City'
                ];
            });

            $countries = Country::search($validated['query'])->get()->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'type' => 'Country'
                ];
            });

            $regions = Region::search($validated['query'])->get()->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'type' => 'Region'
                ];
            });

            $searchResult = $products
                ->merge($cities)
                ->merge($countries)
                ->merge($regions);

            $paginatedResults = PaginationHelper::paginate(
                $searchResult,
                $validated['per_page'] ?? 10,
                $validated['page'] ?? 1
            );

            return response()->json($paginatedResults);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

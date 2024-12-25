<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;

class ProductController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function list(Request $request)
    {
        $products = Product::with(['city', 'user', 'status', 'category'])->get();

        return ApiResponseClass::sendResponse(ProductResource::collection($products), '');
    }

    public function show(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->first();

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function popularDestination(Request $request)
    {
        try {
            $popularDestinations = Product::withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'rating' => $product->reviews_avg_rating
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $popularDestinations,
            ]);
        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
        }
    }

    public function exploreNow(Request $request)
    {
        try {
            $cities = City::with(['products.reviews']) // Load products and reviews
                ->get()
                ->map(function ($city) {
                    // Calculate the average rating for each city based on its products' reviews
                    $reviews = $city->products->flatMap(function ($product) {
                        return $product->reviews;
                    });

                    // Check if reviews exist for the city
                    $averageRating = $reviews->isEmpty() ? 0 : round($reviews->avg('rating'), 1);

                    // Add the average rating to the city
                    $city->average_rating = $averageRating;

                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'rating' => $averageRating, // Return the calculated average rating
                    ];
                })
                ->filter(function ($city) {
                    // Exclude cities with 0 rating
                    return $city['rating'] > 0;
                })
                ->sortByDesc('rating') // Sort by rating in descending order
                ->values() // Re-index the array after filtering
                ->take(5);

            return response()->json([
                'status' => 'success',
                'data' => $cities,
            ]);
        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
        }
    }
}

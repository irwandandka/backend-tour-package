<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use App\Models\Product;
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
}

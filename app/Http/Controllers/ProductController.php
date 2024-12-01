<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
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
}

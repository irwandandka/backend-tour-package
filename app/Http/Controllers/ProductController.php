<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\City;
use Throwable;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Products",
 *     version="1.0.0",
 *     description="API for managing tour package products.",
 *     @OA\Contact(
 *         email="irwndandka@gmail.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * * @OA\Server(
 *     url="https://apilaravel.irwandandka.my.id",
 *     description="Production server for the API"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local development server"
 * )
 */

class ProductController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product/list",
     *     tags={"Product"},
     *     summary="Retrieve a list of all tour packages",
     *     description="Returns a list of available tour packages.",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the list of products",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="List of tour packages.",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="string",
     *                         format="uuid",
     *                         example="0c40bf88-ba9f-11ef-95b1-525400d81c3e",
     *                         description="Unique identifier for the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Paris Art & Culture",
     *                         description="The name of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="slug",
     *                         type="string",
     *                         example="paris-art-&-culture",
     *                         description="URL-friendly version of the package name."
     *                     ),
     *                     @OA\Property(
     *                         property="duration",
     *                         type="string",
     *                         example="2 days",
     *                         description="Duration of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="Dive into the artistic and cultural history of Paris.",
     *                         description="Description of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="integer",
     *                         example=5000000,
     *                         description="Price of the tour package in the smallest currency unit."
     *                     ),
     *                     @OA\Property(
     *                         property="capacity",
     *                         type="integer",
     *                         nullable=true,
     *                         example=null,
     *                         description="Maximum number of participants. Can be null if not specified."
     *                     ),
     *                     @OA\Property(
     *                         property="date_from",
     *                         type="string",
     *                         format="date",
     *                         example="2024-12-12",
     *                         description="Start date of the tour package."
     *                     ),
     *                     @OA\Property(
     *                         property="date_until",
     *                         type="string",
     *                         format="date",
     *                         example="2025-03-12",
     *                         description="End date of the tour package."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function list(Request $request)
    {
        try {
            $products = Product::with(
                [
                    'city',
                    'user',
                    'status',
                    'category'
                ]
            )
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'description' => $product->description,
                        'duration' => $product->duration,
                        'date_from' => $product->date_from,
                        'date_until' => $product->date_until,
                        'price' => $product->price,
                        'capacity' => $product->capacity,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);

            // return ApiResponseClass::sendResponse(ProductResource::collection($products), '');
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $product = Product::with(['city', 'city.country', 'reviews', 'product_details', 'reviews.user'])
                ->where('slug', $slug)
                ->first();

            $resultProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $product->image,
                'duration' => $product->duration,
                'city' => $product->city->name,
                'country' => $product->city->country->name,
                'price' => $product->price,
                'rating' => round($product->reviews->avg('rating'), 1),
                'reviews' => $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user' => $review->user->name,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'review_date' => $review->review_date
                    ];
                }),
                'product_details' => $product->product_details->map(function ($productDetail) {
                    return [
                        'id' => $productDetail->id,
                        'day' => $productDetail->day,
                        'title' => $productDetail->title,
                        'schedule_time' => $productDetail->schedule_time,
                        'image' => $productDetail->activity_image,
                        'description' => $productDetail->description,
                        'latitude' => $productDetail->latitude,
                        'longitude' => $productDetail->longitude,
                    ];
                }),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $resultProduct
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
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
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
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
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

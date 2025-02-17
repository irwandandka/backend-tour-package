<?php

namespace App\Http\Controllers;

use App\Models\{Product, City, Currency};
use App\Services\{ErrorHandler, PricingService};
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Google\Service\CloudIdentity\Group;
use Throwable;

class ProductController extends Controller
{
    /**
     * @see SwaggerInfo::init()
     */

    use ValidatesRequests;

    protected $errorHandler;
    protected $pricingService;

    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->pricingService = new PricingService;
    }

    /**
     * @see SwaggerInfo::list()
     */
    public function list(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'lang' => 'required',
                'currency' => 'required',
            ]);

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $products = Product::with(
                [
                    'city',
                    'city.country',
                    'product_prices',
                    'purchase_currency',
                    'purchase_currency.baseExchangeRates',
                    'sales_currency',
                    'sales_currency.baseExchangeRates',
                ]
            )
                ->get()
                ->map(function ($product) use ($validated, $targetCurrency) {
                    $price = $this->pricingService->getPricing(
                        $product,
                        $validated,
                        $targetCurrency
                    );

                    return [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'description' => $product->description,
                        'duration' => $product->duration,
                        'date_from' => Carbon::parse($product->date_from)->format('l, jS F Y'),
                        'date_until' => Carbon::parse($product->date_until)->format('l, jS F Y'),
                        'price' => formatCurrency($price, $validated['currency']),
                        'capacity' => $product->capacity,
                        'location' => $product->city->name . ', ' . $product->city->country->name,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::show()
     */
    public function show(Request $request, $slug)
    {
        try {
            $validated = $this->validate($request, [
                'lang' => 'required',
                'currency' => 'required',
            ]);

            $product = Product::with(
                [
                    'city',
                    'city.country',
                    'reviews',
                    'product_details',
                    'reviews.user',
                    'product_prices',
                    'purchase_currency',
                    'purchase_currency.baseExchangeRates',
                    'sales_currency',
                    'sales_currency.baseExchangeRates',
                ]
            )
                ->where('slug', $slug)
                ->first();

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $price = $this->pricingService->getPricing(
                $product,
                $validated,
                $targetCurrency
            );

            $resultProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $product->thumbnail_image,
                'duration' => $product->duration,
                'price' => formatCurrency($price, $validated['currency']),
                'rating' => round($product->reviews->avg('rating'), 1),
                'location' => $product->city->name . ', ' . $product->city->country->name,
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

    /**
     * @see SwaggerInfo::popularDestination()
     */
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

    /**
     * @see SwaggerInfo::exploreNow()
     */
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

    /**
     * @see SwaggerInfo::availableDate()
     */
    public function availableDate(Request $request, $slug)
    {
        try {
            $validated = $this->validate($request, [
                'lang' => 'required',
                'currency' => 'required',
            ]);

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $product = Product::with(
                [
                    'Allotments',
                ]
            )
                ->where('slug', $request->slug)->first();

            $tripLength = $product->trip_length;

            $allotments = $product
                ->allotments
                ->where('period', '>=', Carbon::now())
                ->values();

            $now = Carbon::now();
            $dateStart = Carbon::parse($product->date_from);
            $dateEnd = Carbon::parse($product->date_until);
            if ($now < $dateStart) {
                $now = $dateStart;
            }

            $price = $this->pricingService->getPricing(
                $product,
                $validated,
                $targetCurrency
            );

            $result = [];
            for ($currentDate = $dateStart; $currentDate <= $dateEnd; $currentDate->addDay()) {
                $day = $currentDate->day;

                if ($allotments->sum('day' . $day) > 0) {
                    $result[] = [
                        'date_start' => $currentDate->format('l, jS F Y'),
                        'date_end' => $currentDate->addDays($tripLength - 1)->format('l, jS F Y'),
                        'date_start_iso' => $currentDate->format('Y-m-d'),
                        'date_end_iso' => $currentDate->addDays($tripLength - 1)->format('Y-m-d'),
                        'allotment' => $allotments->sum('day' . $day),
                        'price' => formatCurrency($price, $validated['currency']),
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function availablePeriod(Request $request, $slug)
    {
        try {
            $validated = $this->validate($request, [
                'lang' => 'required',
                'currency' => 'required',
            ]);

            $product = Product::with(
                [
                    'Allotments',
                ]
            )
                ->where('slug', $request->slug)
                ->first();

            $availablePeriod = $product
                ->allotments
                ->where('period', '>=', Carbon::now())
                ->groupBy('period')
                ->mapWithKeys(function ($allotments, $period) {
                    $allotments->sum(function ($allotment) {
                        return
                            $allotment->day1 + $allotment->day2 + $allotment->day3 + $allotment->day4 + $allotment->day5 +
                            $allotment->day6 + $allotment->day7 + $allotment->day8 + $allotment->day9 + $allotment->day10 +
                            $allotment->day11 + $allotment->day12 + $allotment->day13 + $allotment->day14 + $allotment->day15 +
                            $allotment->day16 + $allotment->day17 + $allotment->day18 + $allotment->day19 + $allotment->day20 +
                            $allotment->day21 + $allotment->day22 + $allotment->day23 + $allotment->day24 + $allotment->day25 +
                            $allotment->day26 + $allotment->day27 + $allotment->day28 + $allotment->day29 + $allotment->day30 +
                            $allotment->day31;
                    });

                    return [$period];
                })
                ->map(function ($period) {
                    $periodFormat = Carbon::createFromFormat('Ym', $period);

                    return [
                        'id' => $period,
                        'name' => $periodFormat->translatedFormat('F Y'),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $availablePeriod,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

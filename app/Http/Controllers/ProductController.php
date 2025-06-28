<?php

namespace App\Http\Controllers;

use App\Jobs\TestRedisJob;
use App\Models\{Product, City, Currency};
use App\Services\{AllotmentService, ErrorHandler, PackageService, PricingService};
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
    protected $packageService;
    protected $allotmentService;

    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->pricingService = new PricingService;
        $this->packageService = new PackageService;
        $this->allotmentService = new AllotmentService;
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
                    $price = 0;

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

            $dateNow = Carbon::now();

            $product = Product::with(
                [
                    'city',
                    'city.country',
                    'reviews',
                    'product_details',
                    'product_details.allotments',
                    'product_details.product_prices',
                    'reviews.user',
                    'purchase_currency',
                    'purchase_currency.baseExchangeRates',
                    'sales_currency',
                    'sales_currency.baseExchangeRates',
                    'itineraries',
                    'reviews',
                    'reviews.user',
                ]
            )
                ->where('slug', $slug)
                ->first();

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $availableItem = $this
                ->packageService
                ->getAvailableProductDetail(
                    $product,
                    $dateNow,
                    false,
                    true
                );

            $price = $this->pricingService->getPricing(
                $availableItem,
                $validated,
                $targetCurrency
            );

            $itineraries = $product
                ->itineraries
                ->where('language', strtolower($validated['lang']))
                ->sortBy('day')
                ->values()
                ->map(function ($itinerary) {
                    return [
                        'id' => $itinerary->id,
                        'title' => $itinerary->title,
                        'day' => $itinerary->day,
                        'caption' => $itinerary->caption,
                        'description' => $itinerary->description,
                        'schedule_time' => $itinerary->schedule_time,
                        'latitude' => $itinerary->latitude,
                        'longitude' => $itinerary->longitude,
                    ];
                });

            $reviews = $product
                ->reviews
                ->sortByDesc('created_at')
                ->values()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user' => $review->user->name,
                        'email' => $review->user->email,
                        'profile_picture_url' => $review->user->profile_picture_url,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'review_date' => $review->created_at->format('l, jS F Y'),
                    ];
                });

            $duration = $product->trip_length > 1
                ? $product->trip_length . ' Days'
                : $product->trip_length . ' Day';

            if ($product->trip_length > 1) {
                $duration .= ', ' . $product->trip_length - 1 . ' Nights';
            }

            $resultProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $product->thumbnail_image,
                'duration' => $duration,
                'price' => formatCurrency($price, $validated['currency']),
                'rating' => round($product->reviews->avg('rating'), 1),
                'location' => $product->city->name . ', ' . $product->city->country->name,
                'itineraries' => $itineraries,
                'reviews' => $reviews,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $resultProduct
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function roomType(Request $request, $slug)
    {
        try {
            $validated = $this->validate($request, [
                'lang' => 'required|string',
                'currency' => 'required|string',
                'date_start' => 'required|date_format:Y-m-d',
                'date_end' => 'required|date_format:Y-m-d',
            ]);

            $dateStart = Carbon::createFromFormat('Y-m-d', $validated['date_start']);
            $dateEnd = Carbon::createFromFormat('Y-m-d', $validated['date_end']);
            $dateNow = Carbon::now();

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $product = Product::with(
                [
                    'product_details',
                    'product_details.allotments',
                    'product_details.product_prices',
                    'product_details.product.purchase_currency',
                    'product_details.product.sales_currency',
                ]
            )
                ->where('slug', $slug)
                ->first();

            $availableItems = $this
                ->packageService
                ->getAvailableProductDetail($product, $dateStart, true, false)
                ->map(function ($room) use (
                    $targetCurrency,
                    $validated,
                    $dateStart,
                ) {
                    $roomName = $room->{"name_" . strtolower($validated['lang'])} ?? $room->name_en;

                    $priceList = $this->pricingService->getListPricing(
                        $room,
                        $validated,
                        $targetCurrency
                    );

                    $allotments = $this->allotmentService->getAllotment($room, $dateStart);

                    return [
                        "id" => $room->id,
                        "name" => $roomName,
                        "image" => $room->activity_image,
                        "min_adult" => $room->min_adult,
                        "max_adult" => $room->max_adult,
                        "max_pax" => $room->max_pax,
                        "allotment" => $allotments,
                        "pricing" => $priceList,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $availableItems,
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
            $validated = $this->validate($request, [
                'lang' => 'required',
                'currency' => 'required',
            ]);

            $dateNow = Carbon::now();

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $popularDestinations = Product::with(
                [
                    'city',
                    'city.country',
                    'product_details',
                    'product_details.allotments',
                    'product_details.product_prices',
                    'product_details.product.purchase_currency',
                    'product_details.product.sales_currency',
                ]
            )
                ->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->limit(10)
                ->get()
                ->map(function ($product) use ($dateNow, $validated, $targetCurrency) {
                    $availableItem = $this
                        ->packageService
                        ->getAvailableProductDetail(
                            $product,
                            $dateNow,
                            false,
                            true
                        );

                    if (!$availableItem) return null;

                    $price = $this->pricingService->getPricing(
                        $availableItem,
                        $validated,
                        $targetCurrency,
                    );

                    $cityName = $product->city->name;
                    $countryName = $product->city->country->name;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => formatCurrency($price, $validated['currency']),
                        'location' => $cityName . ', ' . $countryName,
                        'image' => $product->thumbnail_image,
                        'rating' => number_format($product->reviews_avg_rating, 1),
                    ];
                })
                ->filter(function ($product) {
                    return $product !== null;
                })
                ->values();

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
            $validated = $this->validate($request, [
                'lang' => 'required',
                'region' => 'string|nullable',
            ]);

            $cities = City::with(['products.reviews']) // Load products and reviews
                ->when(isset($validated['region']), function ($query) use ($validated) {
                    $query->whereHas('country', function ($query) use ($validated) {
                        $query->where('region_id', $validated['region']);
                    });
                })
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
                        'image' => $city->image,
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
                'period' => 'required|date_format:Ym',
            ]);

            $targetCurrency = Currency::where('code', $validated['currency'])->first();

            $dateNow = Carbon::now();

            $product = Product::with(
                [
                    'product_details',
                    'product_details.allotments',
                    'product_details.product_prices',
                    'product_details.product.purchase_currency',
                    'product_details.product.sales_currency',
                ]
            )
                ->where('slug', $request->slug)->first();

            $availableItem = $this
                ->packageService
                ->getAvailableProductDetail(
                    $product,
                    $dateNow,
                    true,
                    true
                );

            $tripLength = $product->trip_length;

            $dateStart = Carbon::createFromFormat('Ym', $validated['period'])
                ->startOfMonth();
            $dateEnd = Carbon::createFromFormat('Ym', $validated['period'])
                ->endOfMonth();

            $price = $this->pricingService->getPricing(
                $availableItem,
                $validated,
                $targetCurrency
            );

            $result = [];

            if ($dateNow->isSameMonth($dateStart)) {
                $dateStart = $dateNow;
            }

            for ($currentDate = $dateStart; $currentDate <= $dateEnd; $currentDate->addDay()) {
                $date = $currentDate;

                $allotments = $this->allotmentService
                    ->getAllotment(
                        $availableItem,
                        $date
                    );

                if ($allotments > 0) {
                    $result[] = [
                        'date_start' => $date->copy()->format('l, jS F Y'),
                        'date_end' => $date->copy()->addDays($tripLength - 1)->format('l, jS F Y'),
                        'date_start_iso' => $date->copy()->format('Y-m-d'),
                        'date_end_iso' => $date->copy()->addDays($tripLength - 1)->format('Y-m-d'),
                        'allotment' => $allotments,
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

    public function testRedis(Request $request)
    {
        try {
            TestRedisJob::dispatch();

            return response()->json([
                'status' => 'success',
                'message' => 'Redis job has been dispatched successfully.',
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\TestRedisJob;
use App\Models\{City};
use App\Services\{AllotmentService, ErrorHandler, PackageService, PricingService};
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Resources\Product\ProductDetailResource;
use App\Http\Resources\Product\RoomTypeResource;
use App\Http\Resources\Product\PopularDestinationResource;
use Illuminate\Http\Request;
use Throwable;

class ProductController extends Controller
{
    /**
     * @see SwaggerInfo::init()
     */

    use ValidatesRequests;

    protected
        $errorHandler,
        $pricingService,
        $packageService,
        $allotmentService;

    public function __construct(
        ErrorHandler $errorHandler,
        PricingService $pricingService,
        PackageService $packageService,
        AllotmentService $allotmentService
    ) {
        $this->errorHandler = $errorHandler;
        $this->pricingService = $pricingService;
        $this->packageService = $packageService;
        $this->allotmentService = $allotmentService;
    }

    /**
     * @see SwaggerInfo::list()
     */
    public function list(Request $request)
    {
        try {
            $products = $this->packageService->list($request);

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
            $product = $this->packageService->detail($request, $slug);

            return response()->json([
                'status' => 'success',
                'data' => new ProductDetailResource($product),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function roomType(Request $request, $slug)
    {
        try {
            $rooms = $this->packageService->getRoomType($request, $slug);

            return response()->json([
                'status' => 'success',
                'data' => RoomTypeResource::collection($rooms),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::popularDestination()
     */
    public function popularDestination(
        Request $request
    ) {
        try {
            $popularDestinations = $this->packageService->popularDestination($request);

            return response()->json([
                'status' => 'success',
                'data' => PopularDestinationResource::collection($popularDestinations),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::exploreNow()
     */
    public function exploreNow(
        Request $request
    ) {
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
            $result = $this->packageService->availableDates($request, $slug);

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
            $availablePeriod = $this->packageService->availablePeriod($request, $slug);

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

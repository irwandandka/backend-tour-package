<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Currency;

class PackageService
{
    public function list(
        Request $request
    ) {
        $validated = $request->validate([
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

        return $products;
    }

    public function detail(
        Request $request,
        string $slug
    ) {
        $validated = $request->validate([
            'lang' => 'required',
            'currency' => 'required',
        ]);

        $pricingService = app(PricingService::class);

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

        $product->currency = $targetCurrency->code;

        $availableItem = $this
            ->getAvailableProductDetail(
                $product,
                $dateNow,
                false,
                true
            );

        $product->price = $pricingService->getPricing(
            $availableItem,
            $validated,
            $targetCurrency
        );

        $product->itineraries = $product
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

        $product->reviews = $product
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

        $product->duration = $product->trip_length > 1
            ? $product->trip_length . ' Days'
            : $product->trip_length . ' Day';

        if ($product->trip_length > 1) {
            $product->duration .= ', ' . $product->trip_length - 1 . ' Nights';
        }

        return $product;
    }

    public function getRoomType(
        Request $request,
        string $slug
    ) {
        $validated = $request->validate([
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

        $pricingService = app(PricingService::class);
        $allotmentService = app(AllotmentService::class);

        $availableItems = $this
            ->getAvailableProductDetail($product, $dateStart, true, false)
            ->map(function ($room) use (
                $targetCurrency,
                $validated,
                $dateStart,
                $pricingService,
                $allotmentService
            ) {
                $roomName = $room->{"name_" . strtolower($validated['lang'])} ?? $room->name_en;

                $priceList = $pricingService->getListPricing(
                    $room,
                    $validated,
                    $targetCurrency
                );

                $allotments = $allotmentService->getAllotment($room, $dateStart);

                return (object)[
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

        return $availableItems;
    }

    public function getAvailableProductDetail(Product $package, Carbon $date, $checkAllotment = false, $singleRow = false)
    {
        $productDetails = $package
            ->product_details
            ->where('is_active', 1)
            ->where('date_from', '<=', $date)
            ->where('date_until', '>=', $date)
            ->sortBy(function ($productDetail) {
                return $productDetail->is_featured ? 0 : 1;
            })
            ->values();

        $data = null;

        $allotmentService = app(AllotmentService::class);

        if ($checkAllotment) {
            $productDetails = $productDetails->filter(function ($productDetail) use ($date, $allotmentService) {
                $allotment = $allotmentService->getAllotment($productDetail, $date);

                return $allotment > 0;
            });
        }

        if ($singleRow) {
            $data = $productDetails->first();
        } else {
            $data = $productDetails;
        }

        return $data;
    }

    public function popularDestination(
        Request $request
    ) {
        $validated = $request->validate([
            'lang' => 'required',
            'currency' => 'required',
        ]);

        $dateNow = Carbon::now();

        $targetCurrency = Currency::where('code', $validated['currency'])->first();

        $pricingService = app(PricingService::class);

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
            ->each(function ($product) use ($dateNow, $validated, $targetCurrency, $pricingService) {
                $availableItem = $this
                    ->getAvailableProductDetail(
                        $product,
                        $dateNow,
                        false,
                        true
                    );

                if (!$availableItem) return null;

                $product->price = $pricingService->getPricing(
                    $availableItem,
                    $validated,
                    $targetCurrency,
                );

                $product->currency = $targetCurrency->code;

                $cityName = $product->city->name;
                $countryName = $product->city->country->name;

                $product->location = $cityName . ', ' . $countryName;

                return $product;
            })
            ->filter(function ($product) {
                return $product !== null;
            })
            ->values();

        return $popularDestinations;
    }

    public function availableDates(
        Request $request,
        string $slug
    ) {
        $validated = $request->validate([
            'lang' => 'required',
            'currency' => 'required',
            'period' => 'required|date_format:Ym',
        ]);

        $targetCurrency = Currency::where('code', $validated['currency'])->first();

        $dateNow = Carbon::now();
        $datePeriod = Carbon::createFromFormat('Ym', $validated['period']);

        $product = Product::with(
            [
                'product_details',
                'product_details.allotments',
                'product_details.product_prices',
                'product_details.product.purchase_currency',
                'product_details.product.sales_currency',
            ]
        )
            ->where('slug', $slug)->first();

        $availableItem = $this
            ->getAvailableProductDetail(
                $product,
                $datePeriod,
                true,
                true
            );

        $tripLength = $product->trip_length;

        $dateStart = Carbon::createFromFormat('Ym', $validated['period'])
            ->startOfMonth();
        $dateEnd = Carbon::createFromFormat('Ym', $validated['period'])
            ->endOfMonth();


        $pricingService = app(PricingService::class);
        $allotmentService = app(AllotmentService::class);

        $price = $pricingService->getPricing(
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

            $allotments = $allotmentService
                ->getAllotment(
                    $availableItem,
                    $date
                );

            if ($allotments > 0) {
                $result[] = [
                    'date_start' => $date->copy()->format('l, j F Y'),
                    'date_end' => $date->copy()->addDays($tripLength - 1)->format('l, j F Y'),
                    'date_start_iso' => $date->copy()->format('Y-m-d'),
                    'date_end_iso' => $date->copy()->addDays($tripLength - 1)->format('Y-m-d'),
                    'allotment' => $allotments,
                    'price' => formatCurrency($price, $validated['currency']),
                ];
            }
        }

        return $result;
    }

    public function availablePeriod(
        Request $request,
        string $slug
    ) {
        $validated = $request->validate([
            'lang' => 'required',
            'currency' => 'required',
        ]);

        $product = Product::with(
            [
                'product_details',
                'product_details.allotments',
            ]
        )
            ->where('slug', $request->slug)
            ->first();

        // Kumpulkan semua allotments dari setiap product_detail
        $allAllotments = $product->product_details
            ->flatMap(function ($detail) {
                return $detail->allotments;
            });

        // Filter allotments berdasarkan period >= sekarang, lalu group by period
        $availablePeriod = $allAllotments
            ->where('period', '>=', Carbon::now())
            ->groupBy('period')
            ->mapWithKeys(function ($allotments, $period) {
                $total = $allotments->sum(function ($allotment) {
                    return collect(range(1, 31))->sum(function ($day) use ($allotment) {
                        return $allotment->{'day' . $day};
                    });
                });

                return [$period => $total];
            })
            ->map(function ($_, $period) {
                $periodFormat = Carbon::createFromFormat('Ym', $period);

                return [
                    'id' => $period,
                    'name' => $periodFormat->translatedFormat('F Y'),
                ];
            })
            ->values();

        return $availablePeriod;
    }
}

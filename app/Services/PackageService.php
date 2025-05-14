<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;

class PackageService
{
    private $allotmentService;

    public function __construct()
    {
        $this->allotmentService = new AllotmentService;
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

        if ($checkAllotment) {
            $productDetails = $productDetails->filter(function ($productDetail) use ($date) {
                $allotment = $this->allotmentService->getAllotment($productDetail, $date);

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
}

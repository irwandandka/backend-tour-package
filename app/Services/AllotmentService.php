<?php

namespace App\Services;

use App\Models\ProductDetail;
use Carbon\Carbon;

class AllotmentService
{
    public function getAllotment(ProductDetail $productDetail, Carbon $date)
    {
        $allotments = $productDetail
            ->allotments
            ->where('period', $date->copy()->format('Ym'))
            ->values()
            ->sum('day'.$date->day);

        return $allotments;
    }
}

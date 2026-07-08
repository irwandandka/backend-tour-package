<?php

namespace Tests\Unit;

use App\Models\Allotment;
use App\Models\ProductDetail;
use App\Services\AllotmentService;
use Carbon\Carbon;
use Tests\TestCase;

class AllotmentServiceTest extends TestCase
{
    private function makeAllotment(string $period, array $days): Allotment
    {
        $allotment = new Allotment(array_merge(['period' => $period, 'code' => 'ALT'], $days));
        $allotment->id = 'allotment-' . uniqid();

        return $allotment;
    }

    private function makeProductDetail(): ProductDetail
    {
        $detail = new ProductDetail(['name_en' => 'Detail', 'activity_image' => 'x.jpg']);
        $detail->id = 'detail-1';

        return $detail;
    }

    public function test_sums_allotment_for_matching_period_and_day(): void
    {
        $service = new AllotmentService();
        $detail = $this->makeProductDetail();

        $allotment1 = $this->makeAllotment('202603', ['day15' => 5]);
        $allotment2 = $this->makeAllotment('202603', ['day15' => 3]);
        $detail->setRelation('allotments', collect([$allotment1, $allotment2]));

        $result = $service->getAllotment($detail, Carbon::parse('2026-03-15'));

        $this->assertSame(8, $result);
    }

    public function test_ignores_allotment_from_a_different_period(): void
    {
        $service = new AllotmentService();
        $detail = $this->makeProductDetail();

        $sameMonth = $this->makeAllotment('202603', ['day15' => 5]);
        $otherMonth = $this->makeAllotment('202602', ['day15' => 100]);
        $detail->setRelation('allotments', collect([$sameMonth, $otherMonth]));

        $result = $service->getAllotment($detail, Carbon::parse('2026-03-15'));

        $this->assertSame(5, $result);
    }

    public function test_handles_first_and_last_day_of_month_boundaries(): void
    {
        $service = new AllotmentService();
        $detail = $this->makeProductDetail();

        $allotment = $this->makeAllotment('202601', ['day1' => 7, 'day31' => 9]);
        $detail->setRelation('allotments', collect([$allotment]));

        $this->assertSame(7, $service->getAllotment($detail, Carbon::parse('2026-01-01')));
        $this->assertSame(9, $service->getAllotment($detail, Carbon::parse('2026-01-31')));
    }

    public function test_returns_zero_when_no_allotment_for_period(): void
    {
        $service = new AllotmentService();
        $detail = $this->makeProductDetail();
        $detail->setRelation('allotments', collect([]));

        $result = $service->getAllotment($detail, Carbon::parse('2026-03-15'));

        $this->assertSame(0, $result);
    }
}

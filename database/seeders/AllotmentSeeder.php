<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllotmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::with(['product_details'])
            ->get();

        DB::transaction(function () use ($products) {
            foreach ($products as $product) {
                foreach ($product->product_details as $detail) {
                    $detail->allotments()->create([
                        'product_detail_id' => $detail->id,
                        'transaction_id' => null,
                        'period' => '202507',
                        'code' => 'Insert-Allotment-202505',
                        'day1' => 25,
                        'day2' => 25,
                        'day3' => 25,
                        'day4' => 25,
                        'day5' => 25,
                        'day6' => 25,
                        'day7' => 25,
                        'day8' => 25,
                        'day9' => 25,
                        'day10' => 25,
                        'day11' => 25,
                        'day12' => 25,
                        'day13' => 25,
                        'day14' => 25,
                        'day15' => 25,
                        'day16' => 25,
                        'day17' => 25,
                        'day18' => 25,
                        'day19' => 25,
                        'day20' => 25,
                        'day21' => 25,
                        'day22' => 25,
                        'day23' => 25,
                        'day24' => 25,
                        'day25' => 25,
                        'day26' => 25,
                        'day27' => 25,
                        'day28' => 25,
                        'day29' => 25,
                        'day30' => 25,
                        'day31' => 25,
                    ]);
                }
            }
        });
    }
}

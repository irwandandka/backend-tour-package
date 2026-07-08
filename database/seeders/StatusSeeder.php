<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Active',
                'code' => 'active',
            ],
            [
                'name' => 'Inactive',
                'code' => 'inactive',
            ],
            [
                'name' => 'Pending',
                'code' => 'pending',
            ],
            [
                'name' => 'Confirmed',
                'code' => 'confirm',
            ],
            [
                'name' => 'Paid',
                'code' => 'paid',
            ],
            [
                'name' => 'Cancelled',
                'code' => 'cancel',
            ],
            [
                'name' => 'Expired',
                'code' => 'expire',
            ],
            [
                'name' => 'Available',
                'code' => 'available',
            ],
            [
                'name' => 'Unavailable',
                'code' => 'unavailable',
            ],
            [
                'name' => 'Failed',
                'code' => 'fail',
            ],
            [
                'name' => 'Refunded',
                'code' => 'refund',
            ],
            [
                'name' => 'Entry',
                'code' => 'entry',
            ],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}

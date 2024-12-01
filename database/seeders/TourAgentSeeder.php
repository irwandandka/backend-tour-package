<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\TourAgent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = Status::where('code', 'active')->first();
        $tourAgents = [
            [
                'name' => 'Tour XYZ',
                'email' => 'xyz@tour.com',
                'phone_number' => '081234567890',
                'address' => 'Jl. Raya No. 123, Jakarta',
                'status_id' => $status->id
            ],
            [
                'name' => 'Wonderful Holidays',
                'email' => 'holidays@wonderful.com',
                'phone_number' => '082233445566',
                'address' => 'Jl. Pahlawan 45, Bali',
                'status_id' => $status->id
            ],
            [
                'name' => 'Adventure Tours',
                'email' => 'info@adventuretours.com',
                'phone_number' => '083344556677',
                'address' => 'Jl. Liburan 10, Surabaya',
                'status_id' => $status->id
            ],
            [
                'name' => 'Family Getaways',
                'email' => 'family@getaways.com',
                'phone_number' => '087766554433',
                'address' => 'Jl. Keluarga 7, Yogyakarta',
                'status_id' => $status->id
            ],
            [
                'name' => 'Luxury Tours',
                'email' => 'luxury@tours.com',
                'phone_number' => '089977889900',
                'address' => 'Jl. Mewah 101, Jakarta',
                'status_id' => $status->id
            ]
        ];

        foreach ($tourAgents as $tourAgent) {
            TourAgent::create($tourAgent);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Adventure'],
            ['name' => 'Cultural'],
            ['name' => 'Historical'],
            ['name' => 'Beach'],
            ['name' => 'Family'],
            ['name' => 'Nature'],
            ['name' => 'City Tour'],
            ['name' => 'Food & Culinary'],
            ['name' => 'Sailing'],
            ['name' => 'Diving'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

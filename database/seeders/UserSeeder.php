<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public static ?User $user = null;

    public function run(): void
    {
        // Insert 1 user, atau ambil kalau sudah ada
        self::$user = User::firstOrCreate(
            ['email' => 'irwandandka29@gmail.com'],
            [
                'name' => 'Irwanda',
                'username' => 'irwandandka29',
                'password' => bcrypt('irwanda123'),
            ]
        );
    }
}

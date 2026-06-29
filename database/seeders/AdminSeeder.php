<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan updateOrCreate agar kebal dari error duplicate
        User::updateOrCreate(
        ['email' => 'admin@gmail.com'], // Patokan pencarian data
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // Silakan ubah passwordnya
                'role' => 'admin',
            ]
        );
    }
}

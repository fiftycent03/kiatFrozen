<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Pastikan hanya ada kode ini, tidak ada query database di sini
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            ShippingRateSeeder::class,
        ]);
    }
}

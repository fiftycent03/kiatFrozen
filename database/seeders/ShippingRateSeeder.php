<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $rates = [
        // --- JAWA TIMUR (Area Utama) ---
        ['province_name' => 'Jawa Timur', 'city_name' => 'Mojokerto', 'district_name' => 'Mojoanyar', 'cost' => 5000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Mojokerto', 'district_name' => 'Jetis', 'cost' => 8000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Mojokerto', 'district_name' => 'Prajurit Kulon', 'cost' => 6000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Mojokerto', 'district_name' => 'Magersari', 'cost' => 6000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Surabaya', 'district_name' => 'Wonokromo', 'cost' => 15000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Surabaya', 'district_name' => 'Gubeng', 'cost' => 15000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Sidoarjo', 'district_name' => 'Waru', 'cost' => 12000],
        ['province_name' => 'Jawa Timur', 'city_name' => 'Malang', 'district_name' => 'Lowokwaru', 'cost' => 25000],

        // --- JAWA TENGAH ---
        ['province_name' => 'Jawa Tengah', 'city_name' => 'Semarang', 'district_name' => 'Semarang Tengah', 'cost' => 45000],
        ['province_name' => 'Jawa Tengah', 'city_name' => 'Surakarta', 'district_name' => 'Laweyan', 'cost' => 40000],
        ['province_name' => 'Jawa Tengah', 'city_name' => 'Magelang', 'district_name' => 'Magelang Selatan', 'cost' => 50000],

        // --- JAWA BARAT & DKI ---
        ['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Selatan', 'district_name' => 'Tebet', 'cost' => 65000],
        ['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Barat', 'district_name' => 'Palmerah', 'cost' => 65000],
        ['province_name' => 'Jawa Barat', 'city_name' => 'Bandung', 'district_name' => 'Coblong', 'cost' => 70000],
        ['province_name' => 'Jawa Barat', 'city_name' => 'Bekasi', 'district_name' => 'Bekasi Barat', 'cost' => 60000],

        // --- BALI ---
        ['province_name' => 'Bali', 'city_name' => 'Denpasar', 'district_name' => 'Denpasar Barat', 'cost' => 85000],
        ['province_name' => 'Bali', 'city_name' => 'Badung', 'district_name' => 'Kuta', 'cost' => 90000],
        ['province_name' => 'Bali', 'city_name' => 'Gianyar', 'district_name' => 'Ubud', 'cost' => 95000],
    ];

    foreach ($rates as $rate) {
        \DB::table('shipping_rates')->updateOrInsert(
            [
                'province_name' => $rate['province_name'], 
                'city_name' => $rate['city_name'], 
                'district_name' => $rate['district_name']
            ],
            [
                'cost' => $rate['cost'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}

}

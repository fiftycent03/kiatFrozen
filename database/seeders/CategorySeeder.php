<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        $data = [
            ['name' => 'Cumi',        'slug' => 'cumi'],
            ['name' => 'Dory',        'slug' => 'dory'],
            ['name' => 'Fillet Ikan', 'slug' => 'fillet ikan'], // sengaja sama dengan di products
            ['name' => 'Kepiting',    'slug' => 'kepiting'],
            ['name' => 'Scallop',     'slug' => 'scallop'],
            ['name' => 'Udang',       'slug' => 'udang'],
        ];

        foreach ($data as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'is_active' => true]
            );
        }
    }
}

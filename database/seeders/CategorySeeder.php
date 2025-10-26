<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Clothing',
            'Books',
            'Home & Kitchen',
            'Beauty',
            'Sports',
            'Toys',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }
}

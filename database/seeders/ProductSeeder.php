<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create 100 fake products using factory
        Product::factory()
        ->count(100)
        ->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $sizes = Size::all();
        foreach ($products as $product) {
            $product->sizes()->attach(
                $sizes->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}

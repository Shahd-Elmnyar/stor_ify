<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $images = Image::all();
        foreach ($products as $product) {
            $product->images()->attach(
                $images->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}

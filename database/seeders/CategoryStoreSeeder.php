<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $stores = Store::all();

        foreach ($categories as $category) {
            $category->stores()->attach(
                $stores->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}

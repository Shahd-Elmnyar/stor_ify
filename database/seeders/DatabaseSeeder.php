<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Favorite;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            OrdersSeeder::class,
            PaymentSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            ColorSeeder::class,
            SizeSeeder::class,
            ImageSeeder::class,
            FavoriteSeeder::class,
            SubCategorySeeder::class,
            BranchSeeder::class,
            CategoryStoreSeeder::class,
            ProductColorSeeder::class,
            ProductSizeSeeder::class,
            ProductImageSeeder::class,
            

        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\Color;
use App\Models\Image;
use App\Models\Store;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        Category::factory()->count(10)->create();
        // // Disable foreign key checks if truncating tables
        // // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // // Truncate tables if necessary
        // // Category::truncate();
        // // Store::truncate();
        // // Branch::truncate();
        // // SubCategory::truncate();
        // // Product::truncate();
        // // Image::truncate();
        // // Color::truncate();
        // // Size::truncate();

        // // Re-enable foreign key checks
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // // Create categories first
        // $categories = Category::factory(5)->create();

        // // Create stores once after categories
        // $stores = Store::factory(10)->create();

        // // Attach stores to categories
        // $categories->each(function ($category) use ($stores) {
        //     $category->stores()->attach($stores->pluck('id')->toArray());
        // });

        // // Create a pool of unique sizes
        // $sizes = collect(['xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl', 'xxxl'])->map(function ($size) {
        //     return Size::create(['size' => $size]);
        // });

        // // Create a pool of unique colors
        // $colors = collect([
        //     'red', 'blue', 'green', 'yellow', 'black', 'white',
        //     'purple', 'orange', 'pink', 'brown', 'gray', 'cyan',
        //     'magenta', 'lime', 'indigo', 'violet', 'gold', 'silver',
        //     'bronze', 'teal', 'navy', 'maroon', 'olive', 'coral'
        // ])->map(function ($color) {
        //     return Color::create(['color' => $color]);
        // });
        // $images = Image::factory(100)->create();

        // $categories->each(function ($category) use ($sizes, $colors, $images) {
        //     // Create branches for each store
        //     $category->stores->each(function ($store) {
        //         Branch::factory(3)->create(['store_id' => $store->id]);
        //     });

        //     $subCategories = SubCategory::factory(5)->create(['category_id' => $category->id]);

        //     $subCategories->each(function ($subCategory) use ($sizes, $colors, $images, $category) {
        //         $category->stores->each(function ($store) use ($subCategory, $sizes, $colors, $images, $category) {
        //             Product::factory(5)
        //                 ->for($store, 'store')
        //                 ->for($subCategory, 'subCategory')
        //                 ->for($category, 'category')
        //                 ->create()
        //                 ->each(function ($product) use ($sizes, $colors, $images) {
        //                     // Assign a random subset of sizes to each product
        //                     $product->sizes()->attach($sizes->random(rand(1, 5))->pluck('id')->toArray());
        //                     // Assign a random subset of colors to each product
        //                     $product->colors()->attach($colors->random(rand(1, 5))->pluck('id')->toArray());
        //                     $product->images()->attach($images->random(rand(1, 3))->pluck('id')->toArray()); // Reduced to 1-3 images
        //                 });
        //         });
        //     });
        // });
    }
}

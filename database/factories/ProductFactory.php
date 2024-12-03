<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $i = 0;
        $i++;

        $productsByCategory = [
            'Electronics' => [
                ['en' => 'iPhone 14 Pro', 'ar' => 'آيفون 14 برو', 'price' => [3999.99, 4999.99]],
                ['en' => 'Samsung Galaxy S23', 'ar' => 'سامسونج جالاكسي اس23', 'price' => [3499.99, 4499.99]],
                ['en' => 'MacBook Pro', 'ar' => 'ماك بوك برو', 'price' => [4999.99, 7999.99]],
                ['en' => 'iPad Air', 'ar' => 'آيباد اير', 'price' => [2499.99, 3499.99]],
                ['en' => 'AirPods Pro', 'ar' => 'ايربودز برو', 'price' => [899.99, 1299.99]],
            ],
            'Fashion' => [
                ['en' => 'Classic T-Shirt', 'ar' => 'تيشيرت كلاسيك', 'price' => [99.99, 199.99]],
                ['en' => 'Leather Bag', 'ar' => 'حقيبة جلد', 'price' => [299.99, 799.99]],
                ['en' => 'Denim Jeans', 'ar' => 'جينز', 'price' => [199.99, 399.99]],
                ['en' => 'Summer Dress', 'ar' => 'فستان صيفي', 'price' => [249.99, 499.99]],
                ['en' => 'Winter Jacket', 'ar' => 'جاكيت شتوي', 'price' => [399.99, 899.99]],
            ],
            'Home & Garden' => [
                ['en' => 'Coffee Maker', 'ar' => 'صانعة قهوة', 'price' => [299.99, 599.99]],
                ['en' => 'Air Purifier', 'ar' => 'منقي هواء', 'price' => [499.99, 999.99]],
                ['en' => 'Blender', 'ar' => 'خلاط', 'price' => [199.99, 399.99]],
                ['en' => 'Garden Tools Set', 'ar' => 'طقم أدوات حديقة', 'price' => [149.99, 299.99]],
                ['en' => 'Vacuum Cleaner', 'ar' => 'مكنسة كهربائية', 'price' => [399.99, 799.99]],
            ],
            'Sports' => [
                ['en' => 'Yoga Mat', 'ar' => 'سجادة يوجا', 'price' => [49.99, 149.99]],
                ['en' => 'Treadmill', 'ar' => 'جهاز مشي', 'price' => [2999.99, 4999.99]],
            ],
            'Beauty' => [
                ['en' => 'Face Cream', 'ar' => 'كريم للوجه', 'price' => [199.99, 399.99]],
                ['en' => 'Perfume', 'ar' => 'عطر', 'price' => [299.99, 999.99]],
            ],
        ];

        $category = array_rand($productsByCategory);
        $product = $this->faker->randomElement($productsByCategory[$category]);

        return [
            'name' => json_encode([
                'en' => $product['en'],
                'ar' => $product['ar']
            ]),
            'description' => json_encode([
                'en' => $this->faker->paragraph(2),
                'ar' => 'وصف المنتج باللغة العربية'
            ]),
            'price' => $this->faker->randomElement($product['price']),
            'discount' => $this->faker->randomElement([0, 10, 15, 20, 25, 30]),
            'store_id' => Store::factory(),
            'sub_category_id' => SubCategory::factory(),
            'category_id' => Category::factory(),
        ];
    }
}

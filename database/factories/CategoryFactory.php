<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $categories = [
        ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
        ['en' => 'Fashion', 'ar' => 'أزياء'],
        ['en' => 'Home & Garden', 'ar' => 'المنزل والحديقة'],
        ['en' => 'Sports', 'ar' => 'رياضة'],
        ['en' => 'Beauty & Health', 'ar' => 'الجمال والصحة'],
        ['en' => 'Automotive', 'ar' => 'سيارات'],
        ['en' => 'Books & Stationery', 'ar' => 'كتب وقرطاسية'],
        ['en' => 'Toys & Games', 'ar' => 'ألعاب'],
        ['en' => 'Groceries', 'ar' => 'بقالة'],
        ['en' => 'Furniture', 'ar' => 'أثاث'],
        ['en' => 'Phones & Tablets', 'ar' => 'هواتف وأجهزة لوحية'],
        ['en' => 'Computers', 'ar' => 'حواسيب'],
        ['en' => 'Watches & Accessories', 'ar' => 'ساعات واكسسوارات'],
        ['en' => 'Baby Products', 'ar' => 'منتجات الأطفال'],
        ['en' => 'Pet Supplies', 'ar' => 'مستلزمات الحيوانات الأليفة'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $index = 0;
        
        // If we've used all categories, reset the index
        if ($index >= count($this->categories)) {
            $index = 0;
        }

        $category = $this->categories[$index];
        $index++;

        return [
            'name' => json_encode($category),
            'description' => json_encode([
                'en' => "Discover our amazing collection of {$category['en']}",
                'ar' => "اكتشف مجموعتنا المذهلة من {$category['ar']}"
            ]),
            'img' => "categories/".($index).".png",
        ];
    }
}

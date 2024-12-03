<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubCategoryFactory extends Factory
{
    protected $subCategories = [
        'Electronics' => [
            ['en' => 'Smartphones', 'ar' => 'هواتف ذكية'],
            ['en' => 'Laptops', 'ar' => 'حواسيب محمولة'],
            ['en' => 'TVs', 'ar' => 'تلفزيونات'],
            ['en' => 'Gaming', 'ar' => 'ألعاب إلكترونية'],
            ['en' => 'Accessories', 'ar' => 'إكسسوارات']
        ],
        'Fashion' => [
            ['en' => 'Men Clothing', 'ar' => 'ملابس رجالية'],
            ['en' => 'Women Clothing', 'ar' => 'ملابس نسائية'],
            ['en' => 'Kids Clothing', 'ar' => 'ملابس أطفال'],
            ['en' => 'Watches', 'ar' => 'ساعات'],
            ['en' => 'Bags', 'ar' => 'حقائب']
        ],
        'default' => [
            ['en' => 'General Items', 'ar' => 'منتجات عامة'],
            ['en' => 'Popular Items', 'ar' => 'منتجات شائعة'],
            ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
            ['en' => 'Featured Items', 'ar' => 'منتجات مميزة'],
            ['en' => 'Special Items', 'ar' => 'منتجات خاصة']
        ]];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $subCategoryIndex = [];

        // Get an existing category or create one if none exist
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        // Get the category name
        $categoryData = json_decode($category->name, true);
        $categoryName = $categoryData['en'] ?? 'default';

        // Get subcategories for this category (or use default if category not found)
        $availableSubCategories = $this->subCategories[$categoryName] 
            ?? $this->subCategories['default'];

        // Initialize index for this category if not exists
        if (!isset($subCategoryIndex[$category->id])) {
            $subCategoryIndex[$category->id] = 0;
        }

        // Get subcategory and increment index
        $index = $subCategoryIndex[$category->id] % count($availableSubCategories);
        $subCategory = $availableSubCategories[$index];
        $subCategoryIndex[$category->id]++;

        return [
            'name' => json_encode($subCategory),
            'category_id' => $category->id,
        ];
    }
}

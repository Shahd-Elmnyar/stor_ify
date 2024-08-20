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
        return [
            'name' => json_encode ([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'description' => json_encode([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'discount' => $this->faker->optional()->numberBetween(0, 100),
            'store_id'=>Store::factory(),
            'sub_category_id'=>SubCategory::factory(),
            'category_id'=>Category::factory(),
        ];
    }
}

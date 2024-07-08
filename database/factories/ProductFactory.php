<?php

namespace Database\Factories;

use App\Models\Store;
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
            'store_id'=>Store::factory(),
            'sub_category_id'=>SubCategory::factory(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'discount' => $this->faker->optional()->numberBetween(0, 100),
        ];
    }
}

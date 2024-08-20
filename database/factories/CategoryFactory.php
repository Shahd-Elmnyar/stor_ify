<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
        return [
            'name' => json_encode([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'description' => json_encode ([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            'img' => $i.".png",
        ];
    }
}

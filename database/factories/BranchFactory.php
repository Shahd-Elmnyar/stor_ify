<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
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
                'en' => $this->faker->company(),
                'ar' => $this->faker->company(),
            ]),
            'address' => json_encode ([
                'en' => $this->faker->address(),
                'ar' => $this->faker->address(),
            ]),
            'store_id' => Store::factory(),
        ];
    }
}

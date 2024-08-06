<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'payment_id' => \App\Models\Payment::factory(),
            'status' => $this->faker->randomElement(['processing', 'completed','pending']),
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'delivery_date' => $this->faker->date(),
            'delivery_time' => $this->faker->time('H:i:s'),
        ];
    }
}

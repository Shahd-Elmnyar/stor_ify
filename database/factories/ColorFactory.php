<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'color' => $this->faker->randomElement([
                'red', 'blue', 'green', 'yellow', 'black', 'white',
                'purple', 'orange', 'pink', 'brown', 'gray', 'cyan',
                'magenta', 'lime', 'indigo', 'violet', 'gold', 'silver',
                'bronze', 'teal', 'navy', 'maroon', 'olive', 'coral'
            ]),
        ];
    }
}

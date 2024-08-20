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
            'color' => json_encode([
                'en' =>
                $this->faker->randomElement([
                    'red',
                    'blue',
                    'green',
                    'yellow',
                    'black',
                    'white',
                    'purple',
                    'orange',
                    'pink',
                    'brown',
                    'gray',
                    'cyan',
                    'magenta',
                    'indigo',
                    'violet',
                    'gold',
                    'silver',
                    'bronze',
                ]),
                'ar' => $this->faker->randomElement([
                    'احمر',
                    'ازرق',
                    'اخضر',
                    'اصفر',
                    'أسود',
                    'ابيض',
                    'بنفسجي',
                    'برتقالي',
                    'وردي',
                    'بني',
                    'رمادي',
                    'سماوي',
                    'أرجواني',
                    'فضي',
                    'نيلي',
                    'زهري',
                    'برونزي',
                    'ذهبي',
                ])
            ]),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $i;
        $i++;
        $stores = [
            // Electronics
            ['en' => 'Apple Store', 'ar' => 'متجر آبل'],
            ['en' => 'Samsung', 'ar' => 'سامسونج'],
            // Fashion
            ['en' => 'Zara', 'ar' => 'زارا'],
            ['en' => 'H&M', 'ar' => 'اتش اند ام'],
            // Home & Garden
            ['en' => 'IKEA', 'ar' => 'ايكيا'],
            ['en' => 'Home Center', 'ar' => 'هوم سنتر'],
            // Sports
            ['en' => 'Nike', 'ar' => 'نايكي'],
            ['en' => 'Adidas', 'ar' => 'اديداس'],
            // Beauty
            ['en' => 'Sephora', 'ar' => 'سيفورا'],
            ['en' => 'Bath & Body Works', 'ar' => 'باث آند بودي وركس'],
            // Supermarkets
            ['en' => 'Carrefour', 'ar' => 'كارفور'],
            ['en' => 'Lulu', 'ar' => 'لولو'],
        ];
        
        return [
            'name' => json_encode($this->faker->randomElement($stores)),
            'img' => "stores/".$i.".png",
        ];
    }
}

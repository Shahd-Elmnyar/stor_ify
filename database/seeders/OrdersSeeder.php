<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all users and products
        $users = User::all();
        $products = Product::all();

        // Create orders and order items
        $users->each(function ($user) use ($products) {
            $orders = Order::factory(rand(1, 3))->create(['user_id' => $user->id]);

            $orders->each(function ($order) use ($products) {
                OrderItem::factory(rand(1, 5))->create([
                    'order_id' => $order->id,
                    'product_id' => $products->random()->id,
                ]);
            });
        });
    }
}

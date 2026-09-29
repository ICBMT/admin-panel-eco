<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds demo data so the Spark Admin panel has something to show.
 *
 * Usage: php artisan db:seed --class=DemoStoreSeeder
 */
class DemoStoreSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $customers = User::factory()->count(3)->create();

        $electronics = Category::create(['name' => 'Electronics', 'description' => 'Gadgets, audio and smart devices.']);
        $fashion = Category::create(['name' => 'Fashion', 'description' => 'Apparel, footwear and accessories.']);
        $home = Category::create(['name' => 'Home & Kitchen', 'description' => 'Everything for a cozy home.']);

        $products = collect([
            ['name' => 'Wireless Headset', 'price' => 129.99, 'stock' => 24, 'category' => $electronics, 'description' => 'Over-ear Bluetooth headset with noise cancelling.'],
            ['name' => 'Gaming Console', 'price' => 499.00, 'stock' => 8, 'category' => $electronics, 'description' => 'Next-gen console with 1TB SSD.'],
            ['name' => 'Smart Watch', 'price' => 219.50, 'stock' => 3, 'category' => $electronics, 'description' => 'Fitness tracking, GPS and heart-rate monitor.'],
            ['name' => 'Premium T-Shirt', 'price' => 35.00, 'stock' => 60, 'category' => $fashion, 'description' => '100% organic cotton, regular fit.'],
            ['name' => 'Oversized Hoodie', 'price' => 89.90, 'stock' => 15, 'category' => $fashion, 'description' => 'Heavyweight fleece hoodie, unisex.'],
            ['name' => 'Running Sneakers', 'price' => 110.00, 'stock' => 0, 'category' => $fashion, 'description' => 'Lightweight cushioned running shoes.'],
            ['name' => 'Espresso Machine', 'price' => 349.00, 'stock' => 6, 'category' => $home, 'description' => '15-bar pump espresso maker with milk frother.'],
            ['name' => 'Cast Iron Skillet', 'price' => 45.00, 'stock' => 2, 'category' => $home, 'description' => 'Pre-seasoned 26cm cast iron skillet.'],
        ])->map(fn (array $attributes) => Product::create([
            'name' => $attributes['name'],
            'price' => $attributes['price'],
            'stock' => $attributes['stock'],
            'category_id' => $attributes['category']->id,
            'description' => $attributes['description'],
        ]));

        $statusFlow = [
            OrderStatus::Delivered,
            OrderStatus::Processing,
            OrderStatus::Pending,
            OrderStatus::Shipped,
            OrderStatus::Cancelled,
            OrderStatus::Delivered,
            OrderStatus::Pending,
            OrderStatus::Delivered,
            OrderStatus::Processing,
            OrderStatus::Cancelled,
            OrderStatus::Delivered,
            OrderStatus::Shipped,
        ];

        foreach ($statusFlow as $index => $status) {
            $customer = $customers[$index % $customers->count()];
            $placedAt = now()->subDays($index % 8)->subHours($index * 3);
            $paymentMethod = $index % 3 === 0 ? 'stripe' : 'cod';

            $items = $products->random(random_int(1, 3));

            $total = 0.0;
            foreach ($items as $product) {
                $total += $product->price * random_int(1, 2);
            }

            /** @var Order $order */
            $order = Order::create([
                'user_id' => $customer->id,
                'total' => $total,
                'status' => $status->value,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'stripe' && $status !== OrderStatus::Cancelled ? 'paid' : ($status === OrderStatus::Delivered ? 'paid' : 'pending'),
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ]);

            $order->statusHistory()->create([
                'status' => OrderStatus::Pending->value,
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ]);

            if ($status !== OrderStatus::Pending) {
                $order->statusHistory()->create([
                    'status' => $status->value,
                    'created_at' => $placedAt->copy()->addHours(4),
                    'updated_at' => $placedAt->copy()->addHours(4),
                ]);
            }

            foreach ($items as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => random_int(1, 2),
                    'price' => $product->price,
                    'created_at' => $placedAt,
                    'updated_at' => $placedAt,
                ]);
            }
        }

        $this->command->info('Demo store data seeded.');
        $this->command->warn("Admin login: {$admin->email} / password");
    }
}

<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        $orders = [
            [
                'name' => 'Office Setup - John Doe',
                'description' => 'Complete office setup for remote work',
                'order_date' => Carbon::now()->subDays(5),
                'status' => 'completed',
                'products' => [
                    ['product_id' => 1, 'quantity' => 1], // Laptop
                    ['product_id' => 2, 'quantity' => 1], // Mouse
                    ['product_id' => 4, 'quantity' => 1], // Keyboard
                    ['product_id' => 5, 'quantity' => 1], // Monitor
                ]
            ],
            [
                'name' => 'Home Office Accessories - Jane Smith',
                'description' => 'Additional accessories for home office',
                'order_date' => Carbon::now()->subDays(3),
                'status' => 'processing',
                'products' => [
                    ['product_id' => 3, 'quantity' => 2], // USB-C Hub
                    ['product_id' => 6, 'quantity' => 1], // Webcam
                    ['product_id' => 7, 'quantity' => 1], // Desk Lamp
                ]
            ],
            [
                'name' => 'Bulk Order - Tech Company',
                'description' => 'Bulk order for new employees',
                'order_date' => Carbon::now()->subDays(1),
                'status' => 'pending',
                'products' => [
                    ['product_id' => 2, 'quantity' => 10], // Mouse
                    ['product_id' => 8, 'quantity' => 10], // Phone Stand
                    ['product_id' => 3, 'quantity' => 5],  // USB-C Hub
                ]
            ],
            [
                'name' => 'Gaming Setup - Mike Johnson',
                'description' => 'Gaming peripherals order',
                'order_date' => Carbon::now(),
                'status' => 'pending',
                'products' => [
                    ['product_id' => 4, 'quantity' => 1], // Mechanical Keyboard
                    ['product_id' => 2, 'quantity' => 1], // Mouse
                    ['product_id' => 5, 'quantity' => 1], // Monitor
                ]
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create([
                'name' => $orderData['name'],
                'description' => $orderData['description'],
                'order_date' => $orderData['order_date'],
                'status' => $orderData['status'],
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($orderData['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $productData['quantity'];
                $totalAmount += $totalPrice;

                $order->products()->attach($product->id, [
                    'quantity' => $productData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // Reduce stock for completed and processing orders
                if (in_array($orderData['status'], ['completed', 'processing'])) {
                    $product->reduceStock($productData['quantity']);
                }
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}

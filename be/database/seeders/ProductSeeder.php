<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'price' => 1299.99,
                'sku' => 'LAPTOP-PRO-15',
                'stock_quantity' => 25,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking',
                'price' => 49.99,
                'sku' => 'MOUSE-WIRELESS-001',
                'stock_quantity' => 100,
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with HDMI, USB ports, and SD card reader',
                'price' => 79.99,
                'sku' => 'HUB-USBC-7IN1',
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical keyboard with blue switches',
                'price' => 129.99,
                'sku' => 'KEYBOARD-MECH-RGB',
                'stock_quantity' => 30,
            ],
            [
                'name' => 'Monitor 27" 4K',
                'description' => '27-inch 4K UHD monitor with USB-C connectivity',
                'price' => 399.99,
                'sku' => 'MONITOR-27-4K',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Webcam HD',
                'description' => '1080p HD webcam with auto-focus and noise reduction',
                'price' => 89.99,
                'sku' => 'WEBCAM-HD-1080',
                'stock_quantity' => 40,
            ],
            [
                'name' => 'Desk Lamp LED',
                'description' => 'Adjustable LED desk lamp with touch controls',
                'price' => 59.99,
                'sku' => 'LAMP-LED-DESK',
                'stock_quantity' => 60,
            ],
            [
                'name' => 'Phone Stand',
                'description' => 'Adjustable aluminum phone and tablet stand',
                'price' => 24.99,
                'sku' => 'STAND-PHONE-ALU',
                'stock_quantity' => 80,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

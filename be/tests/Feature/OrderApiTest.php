<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create test products
        Product::create([
            'name' => 'Test Product 1',
            'description' => 'Test description',
            'price' => 10.00,
            'sku' => 'TEST-001',
            'stock_quantity' => 100,
        ]);

        Product::create([
            'name' => 'Test Product 2',
            'description' => 'Test description 2',
            'price' => 20.00,
            'sku' => 'TEST-002',
            'stock_quantity' => 50,
        ]);
    }

    public function testCanGetOrders()
    {
        $response = $this->get('/api/v1/orders');

        $response->assertResponseOk();
        $response->seeJsonStructure([
            'success',
            'data' => [
                'data',
                'total',
                'per_page',
                'current_page',
            ],
        ]);
    }

    public function testCanCreateOrder()
    {
        $product = Product::first();

        $orderData = [
            'name' => 'Test Order',
            'description' => 'Test order description',
            'order_date' => '2024-01-01',
            'status' => 'pending',
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->post('/api/v1/orders', $orderData);

        $response->assertResponseStatus(201);
        $response->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'order_date',
                'status',
                'total_amount',
                'products',
            ],
        ]);
    }

    public function testCanGetSingleOrder()
    {
        $product = Product::first();
        $order = Order::create([
            'name' => 'Test Order',
            'description' => 'Test description',
            'order_date' => '2024-01-01',
            'status' => 'pending',
            'total_amount' => 20.00,
        ]);

        $order->products()->attach($product->id, [
            'quantity' => 2,
            'unit_price' => $product->price,
            'total_price' => $product->price * 2,
        ]);

        $response = $this->get("/api/v1/orders/{$order->id}");

        $response->assertResponseOk();
        $response->seeJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'order_date',
                'status',
                'total_amount',
                'products',
            ],
        ]);
    }

    public function testCanUpdateOrder()
    {
        $product = Product::first();
        $order = Order::create([
            'name' => 'Test Order',
            'description' => 'Test description',
            'order_date' => '2024-01-01',
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $updateData = [
            'name' => 'Updated Order Name',
            'status' => 'processing',
        ];

        $response = $this->put("/api/v1/orders/{$order->id}", $updateData);

        $response->assertResponseOk();
        $response->seeJson([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }

    public function testCanDeleteOrder()
    {
        $order = Order::create([
            'name' => 'Test Order',
            'description' => 'Test description',
            'order_date' => '2024-01-01',
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $response = $this->delete("/api/v1/orders/{$order->id}");

        $response->assertResponseOk();
        $response->seeJson([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }
}

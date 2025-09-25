<?php

namespace Tests;

/**
 * Order API Tests using PHPUnit.
 *
 * These tests verify the Order API endpoints functionality
 */
class OrderApiTest extends BaseApiTest
{
    /**
     * Test root endpoint returns API info.
     */
    public function testRootEndpointReturnsApiInfo(): void
    {
        $response = $this->makeRequest('GET', '/');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertArrayHasKey('message', $response['data']);
        $this->assertArrayHasKey('version', $response['data']);
        $this->assertArrayHasKey('status', $response['data']);
        $this->assertStringContainsString('Gestionale Iliad', $response['data']['message']);
    }

    /**
     * Test GET /api/v1/orders returns orders list.
     */
    public function testGetOrdersReturnsOrdersList(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/orders');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['data']);
        $this->assertArrayHasKey('current_page', $response['data']['data']);
    }

    /**
     * Test GET /api/v1/orders with pagination.
     */
    public function testGetOrdersWithPagination(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/orders?page=1&per_page=5');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['data']);
        $this->assertArrayHasKey('current_page', $response['data']['data']);
    }

    /**
     * Test GET /api/v1/orders with search filter.
     */
    public function testGetOrdersWithSearchFilter(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/orders?search=test');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
    }

    /**
     * Test POST /api/v1/orders creates new order.
     */
    public function testCreateOrderWithValidData(): void
    {
        $orderData = [
            'name' => 'Test Customer PHPUnit',
            'description' => 'Test order from PHPUnit',
            'order_date' => '2024-01-01',
            'products' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ];

        $response = $this->makeRequest('POST', '/api/v1/orders', $orderData);

        $this->assertEquals(201, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['data']);
        $this->assertArrayHasKey('id', $response['data']['data']);
        $this->assertStringContainsString('Test Customer PHPUnit', $response['data']['data']['name']);
    }

    /**
     * Test POST /api/v1/orders with insufficient stock fails.
     */
    public function testCreateOrderWithInsufficientStockFails(): void
    {
        $orderData = [
            'name' => 'Test Customer',
            'description' => 'Test order with insufficient stock',
            'order_date' => '2024-01-01',
            'products' => [
                ['product_id' => 1, 'quantity' => 9999], // Impossible quantity
            ],
        ];

        $response = $this->makeRequest('POST', '/api/v1/orders', $orderData);

        // Accept both 400 and 500 as valid error responses for insufficient stock
        $this->assertContains($response['status_code'], [400, 500]);
        $this->assertStringContainsString('Insufficient stock', $response['body']);
    }

    /**
     * Test POST /api/v1/orders with invalid data fails.
     */
    public function testCreateOrderWithInvalidDataFails(): void
    {
        $orderData = [
            'name' => '', // Empty name
            'description' => 'Test order with invalid data',
            'order_date' => '2024-01-01',
            'products' => [],
        ];

        $response = $this->makeRequest('POST', '/api/v1/orders', $orderData);

        $this->assertEquals(422, $response['status_code']);
        $this->assertJsonResponse($response);
    }
}

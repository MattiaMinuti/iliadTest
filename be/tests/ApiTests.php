<?php

require_once __DIR__ . '/TestFramework.php';

/**
 * API Tests for Gestionale Iliad
 * 
 * These tests verify that all API endpoints are working correctly
 * and maintain backward compatibility.
 */

class ApiTests {
    private $testFramework;
    
    public function __construct() {
        $this->testFramework = new TestFramework();
    }
    
    /**
     * Run all API tests
     */
    public function runAll() {
        echo "🚀 Starting Gestionale Iliad API Tests\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $this->testRootEndpoint();
        $this->testOrdersEndpoints();
        $this->testProductsEndpoints();
        $this->testSwaggerEndpoints();
        
        return $this->testFramework->printSummary();
    }
    
    /**
     * Test root endpoint
     */
    private function testRootEndpoint() {
        $this->testFramework->test('Root endpoint returns API info', function($t) {
            $response = $t->request('GET', '/');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('message', $response['data']);
            $t->assertArrayHasKey('version', $response['data']);
            $t->assertArrayHasKey('status', $response['data']);
            $t->assertStringContains('Gestionale Iliad', $response['data']['message']);
        });
    }
    
    /**
     * Test orders endpoints
     */
    private function testOrdersEndpoints() {
        $this->testFramework->test('GET /api/v1/orders returns orders list', function($t) {
            $response = $t->request('GET', '/api/v1/orders');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('success', $response['data']);
            $t->assertArrayHasKey('data', $response['data']);
            $t->assertArrayHasKey('current_page', $response['data']['data']);
        });
        
        $this->testFramework->test('GET /api/v1/orders with pagination', function($t) {
            $response = $t->request('GET', '/api/v1/orders?page=1&per_page=5');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('success', $response['data']);
            $t->assertArrayHasKey('data', $response['data']);
            $t->assertArrayHasKey('current_page', $response['data']['data']);
        });
        
        $this->testFramework->test('GET /api/v1/orders with search filter', function($t) {
            $response = $t->request('GET', '/api/v1/orders?search=test');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
        });
        
        $this->testFramework->test('POST /api/v1/orders creates new order', function($t) {
            $orderData = [
                'name' => 'Test Customer API',
                'description' => 'Test order from API',
                'order_date' => '2024-01-01',
                'products' => [
                    ['product_id' => 1, 'quantity' => 1]
                ]
            ];
            
            $response = $t->request('POST', '/api/v1/orders', $orderData);
            
            $t->assertStatus(201, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('success', $response['data']);
            $t->assertArrayHasKey('data', $response['data']);
            $t->assertArrayHasKey('id', $response['data']['data']);
            $t->assertStringContains('Test Customer API', $response['data']['data']['name']);
        });
        
        $this->testFramework->test('POST /api/v1/orders with insufficient stock fails', function($t) {
            $orderData = [
                'name' => 'Test Customer',
                'description' => 'Test order with insufficient stock',
                'order_date' => '2024-01-01',
                'products' => [
                    ['product_id' => 1, 'quantity' => 9999] // Impossible quantity
                ]
            ];
            
            $response = $t->request('POST', '/api/v1/orders', $orderData);
            
            // Accept both 400 and 500 as valid error responses for insufficient stock
            if ($response['status_code'] === 400 || $response['status_code'] === 500) {
                $t->assertStringContains('Insufficient stock', $response['body']);
            } else {
                throw new Exception("Expected status 400 or 500, got {$response['status_code']}");
            }
        });
        
        $this->testFramework->test('POST /api/v1/orders with invalid data fails', function($t) {
            $orderData = [
                'name' => '', // Empty name
                'description' => 'Test order with invalid data',
                'order_date' => '2024-01-01',
                'products' => []
            ];
            
            $response = $t->request('POST', '/api/v1/orders', $orderData);
            
            $t->assertStatus(422, $response['status_code']);
        });
    }
    
    /**
     * Test products endpoints
     */
    private function testProductsEndpoints() {
        $this->testFramework->test('GET /api/v1/products returns products list', function($t) {
            $response = $t->request('GET', '/api/v1/products');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('data', $response['data']);
        });
        
        $this->testFramework->test('GET /api/v1/products/{id} returns specific product', function($t) {
            $response = $t->request('GET', '/api/v1/products/1');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('data', $response['data']);
            $t->assertArrayHasKey('id', $response['data']['data']);
        });
        
        $this->testFramework->test('GET /api/v1/products/{id} with non-existent ID returns 404', function($t) {
            $response = $t->request('GET', '/api/v1/products/99999');
            
            $t->assertStatus(404, $response['status_code']);
        });
    }
    
    /**
     * Test Swagger documentation endpoints
     */
    private function testSwaggerEndpoints() {
        $this->testFramework->test('GET /swagger returns Swagger UI', function($t) {
            $response = $t->request('GET', '/swagger');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertStringContains('Gestionale Iliad API Documentation', $response['body']);
            $t->assertStringContains('swagger-ui', $response['body']);
        });
        
        $this->testFramework->test('GET /api/documentation returns OpenAPI JSON', function($t) {
            $response = $t->request('GET', '/api/documentation');
            
            $t->assertStatus(200, $response['status_code']);
            $t->assertJson($response['body']);
            $t->assertArrayHasKey('openapi', $response['data']);
            $t->assertArrayHasKey('info', $response['data']);
            $t->assertStringContains('Gestionale Iliad API', $response['data']['info']['title']);
        });
    }
}

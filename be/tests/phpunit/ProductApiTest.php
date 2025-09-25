<?php

namespace Tests;

/**
 * Product API Tests using PHPUnit.
 *
 * These tests verify the Product API endpoints functionality
 */
class ProductApiTest extends BaseApiTest
{
    /**
     * Test GET /api/v1/products returns products list.
     */
    public function testGetProductsReturnsProductsList(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/products');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['data']);
    }

    /**
     * Test GET /api/v1/products/{id} returns specific product.
     */
    public function testGetProductByIdReturnsSpecificProduct(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/products/1');

        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['data']);
        $this->assertArrayHasKey('id', $response['data']['data']);
    }

    /**
     * Test GET /api/v1/products/{id} with non-existent ID returns 404.
     */
    public function testGetProductByNonExistentIdReturns404(): void
    {
        $response = $this->makeRequest('GET', '/api/v1/products/99999');

        $this->assertEquals(404, $response['status_code']);
        $this->assertJsonResponse($response);
    }
}

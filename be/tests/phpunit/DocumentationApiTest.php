<?php

namespace Tests;

/**
 * Documentation API Tests using PHPUnit
 * 
 * These tests verify the Documentation API endpoints functionality
 */
class DocumentationApiTest extends BaseApiTest
{
    /**
     * Test GET /swagger returns Swagger UI
     */
    public function testGetSwaggerReturnsSwaggerUi(): void
    {
        $response = $this->makeRequest('GET', '/swagger');
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertStringContainsString('Gestionale Iliad API Documentation', $response['body']);
        $this->assertStringContainsString('swagger-ui', $response['body']);
    }
    
    /**
     * Test GET /api/documentation returns OpenAPI JSON
     */
    public function testGetApiDocumentationReturnsOpenApiJson(): void
    {
        $response = $this->makeRequest('GET', '/api/documentation');
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertJsonResponse($response);
        $this->assertArrayHasKey('openapi', $response['data']);
        $this->assertArrayHasKey('info', $response['data']);
        $this->assertStringContainsString('Gestionale Iliad API', $response['data']['info']['title']);
    }
}

<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base API Test Class for Gestionale Iliad.
 *
 * This class provides common functionality for all API tests
 */
abstract class BaseApiTest extends TestCase
{
    protected $baseUrl;

    protected $timeout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = TEST_BASE_URL;
        $this->timeout = TEST_TIMEOUT;

        // Verify backend is running
        $this->assertBackendIsRunning();
    }

    /**
     * Make HTTP request to API.
     */
    protected function makeRequest(string $method, string $endpoint, array $data = null, array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers['Content-Type'] = 'application/json';
        }

        if (! empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->fail("cURL Error: {$error}");
        }

        return [
            'status_code' => $httpCode,
            'body' => $response,
            'data' => json_decode($response, true),
        ];
    }

    /**
     * Assert that backend is running.
     */
    protected function assertBackendIsRunning(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->markTestSkipped('Backend is not running at ' . $this->baseUrl);
        }
    }

    /**
     * Assert JSON response structure.
     */
    protected function assertJsonResponse(array $response): void
    {
        $this->assertIsString($response['body']);
        $this->assertJson($response['body']);
        $this->assertIsArray($response['data']);
    }

    /**
     * Assert API success response.
     */
    protected function assertApiSuccess(array $response): void
    {
        $this->assertArrayHasKey('success', $response['data']);
        $this->assertTrue($response['data']['success']);
    }

    /**
     * Format headers for cURL.
     */
    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }
}

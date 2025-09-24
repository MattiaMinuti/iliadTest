<?php

/**
 * Lightweight Test Framework for Gestionale Iliad API
 * 
 * This framework provides a simple way to test API endpoints
 * without external dependencies, keeping the project optimized.
 */

class TestFramework {
    private $baseUrl;
    private $testResults = [];
    private $currentTest = '';
    
    public function __construct($baseUrl = 'http://localhost:8000') {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    /**
     * Run a single test
     */
    public function test($name, $callback) {
        $this->currentTest = $name;
        echo "🧪 Running test: {$name}\n";
        
        try {
            $callback($this);
            $this->testResults[$name] = ['status' => 'PASS', 'message' => 'Test passed'];
            echo "✅ {$name}: PASSED\n";
        } catch (Exception $e) {
            $this->testResults[$name] = ['status' => 'FAIL', 'message' => $e->getMessage()];
            echo "❌ {$name}: FAILED - {$e->getMessage()}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Make HTTP request to API
     */
    public function request($method, $endpoint, $data = null, $headers = []) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers['Content-Type'] = 'application/json';
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: {$error}");
        }
        
        return [
            'status_code' => $httpCode,
            'body' => $response,
            'data' => json_decode($response, true)
        ];
    }
    
    /**
     * Assertions
     */
    public function assertStatus($expected, $actual) {
        if ($expected !== $actual) {
            throw new Exception("Expected status {$expected}, got {$actual}");
        }
    }
    
    public function assertJson($response) {
        if (json_decode($response) === null) {
            throw new Exception("Response is not valid JSON");
        }
    }
    
    public function assertArrayHasKey($key, $array) {
        if (!array_key_exists($key, $array)) {
            throw new Exception("Array does not contain key '{$key}'");
        }
    }
    
    public function assertCount($expected, $array) {
        if (count($array) !== $expected) {
            throw new Exception("Expected count {$expected}, got " . count($array));
        }
    }
    
    public function assertStringContains($needle, $haystack) {
        if (strpos($haystack, $needle) === false) {
            throw new Exception("String does not contain '{$needle}'");
        }
    }
    
    public function assertNotEmpty($value) {
        if (empty($value)) {
            throw new Exception("Value is empty");
        }
    }
    
    /**
     * Get test results summary
     */
    public function getResults() {
        $total = count($this->testResults);
        $passed = count(array_filter($this->testResults, function($result) {
            return $result['status'] === 'PASS';
        }));
        $failed = $total - $passed;
        
        return [
            'total' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'results' => $this->testResults
        ];
    }
    
    /**
     * Print test summary
     */
    public function printSummary() {
        $results = $this->getResults();
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🧪 TEST SUMMARY\n";
        echo str_repeat("=", 50) . "\n";
        echo "Total tests: {$results['total']}\n";
        echo "✅ Passed: {$results['passed']}\n";
        echo "❌ Failed: {$results['failed']}\n";
        echo str_repeat("=", 50) . "\n";
        
        if ($results['failed'] > 0) {
            echo "\n❌ FAILED TESTS:\n";
            foreach ($results['results'] as $testName => $result) {
                if ($result['status'] === 'FAIL') {
                    echo "- {$testName}: {$result['message']}\n";
                }
            }
        }
        
        return $results['failed'] === 0;
    }
    
    private function formatHeaders($headers) {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }
}

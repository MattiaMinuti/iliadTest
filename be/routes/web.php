<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () use ($router) {
    return response()->json([
        'message' => 'Gestionale Iliad API',
        'version' => $router->app->version(),
        'status' => 'running',
        'documentation' => 'http://localhost:8000/swagger',
        'openapi_json' => 'http://localhost:8000/api/documentation'
    ]);
});

// API Documentation
$router->get('/api/documentation', function () {
    return response()->json([
        "openapi" => "3.0.0",
        "info" => [
            "title" => "Gestionale Iliad API",
            "version" => "1.0.0",
            "description" => "RESTful API per la gestione ordini e prodotti del Gestionale Iliad"
        ],
        "servers" => [
            ["url" => "http://localhost:8000", "description" => "Local Development Server"],
            ["url" => "http://backend:8000", "description" => "Docker Development Server"]
        ],
        "paths" => [
            "/api/v1/orders" => [
                "get" => [
                    "tags" => ["Orders"],
                    "summary" => "Get paginated list of orders",
                    "description" => "Retrieve a paginated list of orders with optional filtering",
                    "parameters" => [
                        ["name" => "page", "in" => "query", "schema" => ["type" => "integer", "example" => 1]],
                        ["name" => "per_page", "in" => "query", "schema" => ["type" => "integer", "example" => 15]],
                        ["name" => "start_date", "in" => "query", "schema" => ["type" => "string", "format" => "date"]],
                        ["name" => "end_date", "in" => "query", "schema" => ["type" => "string", "format" => "date"]],
                        ["name" => "status", "in" => "query", "schema" => ["type" => "string", "enum" => ["pending", "processing", "completed", "cancelled"]]],
                        ["name" => "search", "in" => "query", "schema" => ["type" => "string"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Successful operation"]
                    ]
                ],
                "post" => [
                    "tags" => ["Orders"],
                    "summary" => "Create a new order",
                    "description" => "Create a new order with products",
                    "requestBody" => [
                        "required" => true,
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "required" => ["name", "order_date", "products"],
                                    "properties" => [
                                        "name" => ["type" => "string", "example" => "Order #001"],
                                        "description" => ["type" => "string", "nullable" => true],
                                        "order_date" => ["type" => "string", "format" => "date", "example" => "2024-01-01"],
                                        "status" => ["type" => "string", "enum" => ["pending", "processing", "completed", "cancelled"]],
                                        "products" => [
                                            "type" => "array",
                                            "items" => [
                                                "type" => "object",
                                                "required" => ["product_id", "quantity"],
                                                "properties" => [
                                                    "product_id" => ["type" => "integer", "example" => 1],
                                                    "quantity" => ["type" => "integer", "minimum" => 1, "example" => 2]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "responses" => [
                        "201" => ["description" => "Order created successfully"],
                        "400" => ["description" => "Insufficient stock or validation error"]
                    ]
                ]
            ],
            "/api/v1/orders/{id}" => [
                "get" => [
                    "tags" => ["Orders"],
                    "summary" => "Get order by ID",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Successful operation"],
                        "404" => ["description" => "Order not found"]
                    ]
                ],
                "delete" => [
                    "tags" => ["Orders"],
                    "summary" => "Delete order",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Order deleted successfully"],
                        "404" => ["description" => "Order not found"]
                    ]
                ]
            ],
            "/api/v1/products" => [
                "get" => [
                    "tags" => ["Products"],
                    "summary" => "Get paginated list of products",
                    "parameters" => [
                        ["name" => "page", "in" => "query", "schema" => ["type" => "integer"]],
                        ["name" => "per_page", "in" => "query", "schema" => ["type" => "integer"]],
                        ["name" => "stock_status", "in" => "query", "schema" => ["type" => "string", "enum" => ["in_stock", "out_of_stock"]]],
                        ["name" => "search", "in" => "query", "schema" => ["type" => "string"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Successful operation"]
                    ]
                ],
                "post" => [
                    "tags" => ["Products"],
                    "summary" => "Create a new product",
                    "requestBody" => [
                        "required" => true,
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "required" => ["name", "price", "sku", "stock_quantity"],
                                    "properties" => [
                                        "name" => ["type" => "string", "example" => "Laptop Pro 15\""],
                                        "description" => ["type" => "string", "nullable" => true],
                                        "price" => ["type" => "number", "format" => "decimal", "minimum" => 0, "example" => 1299.99],
                                        "sku" => ["type" => "string", "example" => "LAPTOP-PRO-15"],
                                        "stock_quantity" => ["type" => "integer", "minimum" => 0, "example" => 25]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "responses" => [
                        "201" => ["description" => "Product created successfully"]
                    ]
                ]
            ]
        ]
    ]);
});

// Swagger UI
$router->get('/swagger', function () {
    return response()->file(base_path('public/swagger.html'));
});

// API Routes
$router->group(['prefix' => 'api/v1'], function () use ($router) {
    
    // Orders routes
    $router->get('orders', 'OrderController@index');
    $router->post('orders', 'OrderController@store');
    $router->get('orders/{id}', 'OrderController@show');
    $router->put('orders/{id}', 'OrderController@update');
    $router->delete('orders/{id}', 'OrderController@destroy');
    
    // Products routes
    $router->get('products', 'ProductController@index');
    $router->post('products', 'ProductController@store');
    $router->get('products/{id}', 'ProductController@show');
    $router->put('products/{id}', 'ProductController@update');
    $router->delete('products/{id}', 'ProductController@destroy');
    
});

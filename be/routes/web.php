<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () use ($router) {
    return response()->json([
        'message' => 'Order Management API',
        'version' => $router->app->version(),
        'status' => 'running'
    ]);
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

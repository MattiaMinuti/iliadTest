<?php

namespace App\Providers;

use App\Dao\OrderDao;
use App\Dao\ProductDao;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register DAOs
        $this->app->singleton(OrderDao::class, function ($app) {
            return new OrderDao($app->make(Order::class));
        });

        $this->app->singleton(ProductDao::class, function ($app) {
            return new ProductDao($app->make(Product::class));
        });

        // Register Services
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderDao::class),
                $app->make(ProductDao::class)
            );
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService($app->make(ProductDao::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

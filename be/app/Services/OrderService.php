<?php

namespace App\Services;

use App\Dao\OrderDao;
use App\Dao\ProductDao;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected $productDao;

    public function __construct(OrderDao $orderDao, ProductDao $productDao)
    {
        parent::__construct($orderDao);
        $this->productDao = $productDao;
    }

    /**
     * Get orders with filters
     */
    public function getOrdersWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->dao->getWithFilters($filters, $perPage);
    }

    /**
     * Get order with products
     */
    public function getOrderWithProducts(int $id): ?Order
    {
        return $this->dao->findByIdWithProducts($id);
    }

    /**
     * Create order with business logic validation
     */
    public function createOrder(array $orderData): Order
    {
        // Validate products availability
        $this->validateProductsAvailability($orderData['products']);

        // Calculate total amount
        $totalAmount = $this->calculateTotalAmount($orderData['products']);

        // Create order
        $order = $this->dao->create([
            'name' => $orderData['name'],
            'description' => $orderData['description'],
            'order_date' => $orderData['order_date'],
            'status' => $orderData['status'] ?? 'pending',
            'total_amount' => $totalAmount,
        ]);

        // Attach products and reduce stock
        $this->attachProductsAndReduceStock($order, $orderData['products']);

        return $order->load('products');
    }

    /**
     * Update order with business logic validation
     */
    public function updateOrder(Order $order, array $orderData): Order
    {
        // Update basic order information
        $this->dao->update($order, $orderData);

        // Update products if provided
        if (isset($orderData['products'])) {
            $this->updateOrderProducts($order, $orderData['products']);
        }

        return $order->load('products');
    }

    /**
     * Delete order and restore stock
     */
    public function deleteOrder(Order $order): bool
    {
        // Restore stock for all products in the order
        foreach ($order->products as $product) {
            $this->productDao->updateStock($product, $product->stock_quantity + $product->pivot->quantity);
        }

        // Delete the order
        return $this->dao->delete($order);
    }

    /**
     * Validate products availability
     */
    private function validateProductsAvailability(array $products): void
    {
        foreach ($products as $productData) {
            $product = $this->productDao->findByIdOrFail($productData['product_id']);
            
            if (!$product->hasStock($productData['quantity'])) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }
        }
    }

    /**
     * Calculate total amount for products
     */
    private function calculateTotalAmount(array $products): float
    {
        $totalAmount = 0;

        foreach ($products as $productData) {
            $product = $this->productDao->findByIdOrFail($productData['product_id']);
            $totalAmount += $product->price * $productData['quantity'];
        }

        return $totalAmount;
    }

    /**
     * Attach products to order and reduce stock
     */
    private function attachProductsAndReduceStock(Order $order, array $products): void
    {
        $productsToAttach = [];

        foreach ($products as $productData) {
            $product = $this->productDao->findByIdOrFail($productData['product_id']);
            
            $productsToAttach[] = [
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $product->price,
                'total_price' => $product->price * $productData['quantity'],
            ];

            // Reduce stock
            $this->productDao->updateStock($product, $product->stock_quantity - $productData['quantity']);
        }

        $this->dao->attachProducts($order, $productsToAttach);
    }

    /**
     * Update order products with validation
     */
    private function updateOrderProducts(Order $order, array $newProducts): void
    {
        // Validate new products availability (considering current order stock)
        $this->validateProductsAvailabilityForUpdate($order, $newProducts);

        // Calculate new total amount
        $totalAmount = $this->calculateTotalAmount($newProducts);

        // Restore stock from current products
        foreach ($order->products as $currentProduct) {
            $this->productDao->updateStock($currentProduct, $currentProduct->stock_quantity + $currentProduct->pivot->quantity);
        }

        // Clear current products
        $this->dao->detachAllProducts($order);

        // Attach new products and reduce stock
        $this->attachProductsAndReduceStock($order, $newProducts);

        // Update total amount
        $this->dao->update($order, ['total_amount' => $totalAmount]);
    }

    /**
     * Validate products availability for update (considering current order stock)
     */
    private function validateProductsAvailabilityForUpdate(Order $order, array $newProducts): void
    {
        foreach ($newProducts as $productData) {
            $product = $this->productDao->findByIdOrFail($productData['product_id']);
            
            // Calculate available stock (current stock + what we'll restore from this order)
            $currentOrderQuantity = 0;
            $currentProduct = $order->products->where('id', $product->id)->first();
            if ($currentProduct) {
                $currentOrderQuantity = $currentProduct->pivot->quantity;
            }
            
            $availableStock = $product->stock_quantity + $currentOrderQuantity;
            
            if ($availableStock < $productData['quantity']) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }
        }
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus(string $status): Collection
    {
        return $this->dao->getByStatus($status);
    }

    /**
     * Get orders by date range
     */
    public function getOrdersByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->dao->getByDateRange($startDate, $endDate);
    }
}

<?php

namespace App\Services;

use App\Dao\ProductDao;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    public function __construct(ProductDao $productDao)
    {
        parent::__construct($productDao);
    }

    /**
     * Get products with filters.
     */
    public function getProductsWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->dao->getWithFilters($filters, $perPage);
    }

    /**
     * Create product with validation.
     * @throws Exception
     */
    public function createProduct(array $productData): Product
    {
        // Validate SKU uniqueness
        if ($this->dao->findBySku($productData['sku'])) {
            throw new Exception("Product with SKU '{$productData['sku']}' already exists");
        }

        return $this->dao->create($productData);
    }

    /**
     * Update product with validation.
     */
    public function updateProduct(Product $product, array $productData): Product
    {
        // Validate SKU uniqueness (excluding current product)
        if (isset($productData['sku'])) {
            $existingProduct = $this->dao->findBySku($productData['sku']);
            if ($existingProduct && $existingProduct->id !== $product->id) {
                throw new Exception("Product with SKU '{$productData['sku']}' already exists");
            }
        }

        $this->dao->update($product, $productData);
        return $product->fresh();
    }

    /**
     * Delete product with validation.
     * @throws Exception
     */
    public function deleteProduct(Product $product): bool
    {
        // Check if product is associated with any orders
        if ($product->orders()->exists()) {
            throw new Exception('Cannot delete product that is associated with orders');
        }

        return $this->dao->delete($product);
    }

    /**
     * Update product stock.
     * @throws Exception
     */
    public function updateStock(Product $product, int $newStock): Product
    {
        if ($newStock < 0) {
            throw new Exception('Stock quantity cannot be negative');
        }

        $this->dao->updateStock($product, $newStock);
        return $product->fresh();
    }

    /**
     * Reduce product stock.
     * @throws Exception
     */
    public function reduceStock(Product $product, int $quantity): Product
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than 0');
        }

        if (! $product->hasStock($quantity)) {
            throw new Exception("Insufficient stock for product: {$product->name}");
        }

        $newStock = $product->stock_quantity - $quantity;
        $this->dao->updateStock($product, $newStock);

        return $product->fresh();
    }

    /**
     * Increase product stock.
     * @throws Exception
     */
    public function increaseStock(Product $product, int $quantity): Product
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than 0');
        }

        $newStock = $product->stock_quantity + $quantity;
        $this->dao->updateStock($product, $newStock);

        return $product->fresh();
    }

    /**
     * Get products by stock level.
     */
    public function getProductsByStockLevel(int $minStock = 0): Collection
    {
        return $this->dao->getByStockLevel($minStock);
    }

    /**
     * Get out of stock products.
     */
    public function getOutOfStockProducts(): Collection
    {
        return $this->dao->getOutOfStock();
    }

    /**
     * Get low stock products.
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return $this->dao->getLowStock($threshold);
    }

}

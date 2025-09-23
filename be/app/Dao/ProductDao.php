<?php

namespace App\Dao;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductDao extends BaseDao
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    /**
     * Get products with filters
     */
    public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Stock status filter
        if (isset($filters['stock_status']) && $filters['stock_status']) {
            if ($filters['stock_status'] === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            }
        }

        // Search filter
        if (isset($filters['search']) && $filters['search']) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get products by stock level
     */
    public function getByStockLevel(int $minStock = 0): Collection
    {
        return $this->model->where('stock_quantity', '>=', $minStock)->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock(): Collection
    {
        return $this->model->where('stock_quantity', '=', 0)->get();
    }

    /**
     * Get low stock products (less than threshold)
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->model->where('stock_quantity', '>', 0)
                          ->where('stock_quantity', '<=', $threshold)
                          ->get();
    }

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Product $product, int $newStock): bool
    {
        return $product->update(['stock_quantity' => $newStock]);
    }

    /**
     * Get products with orders relationship
     */
    public function getWithOrders(): Collection
    {
        return $this->model->with('orders')->get();
    }

    /**
     * Get products that have been ordered
     */
    public function getOrderedProducts(): Collection
    {
        return $this->model->whereHas('orders')->get();
    }
}

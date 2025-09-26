<?php

namespace App\Dao;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderDao extends BaseDao
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    /**
     * Get orders with products relationship.
     */
    public function getWithProducts(): Collection
    {
        return $this->model->with('products')->get();
    }

    /**
     * Get order by ID with products.
     */
    public function findByIdWithProducts(int $id): ?Order
    {
        return $this->model->with('products')->find($id);
    }

    /**
     * Get orders with filters.
     */
    public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Date range filter
        if (isset($filters['start_date']) && $filters['start_date']) {
            $query->where('order_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && $filters['end_date']) {
            $query->where('order_date', '<=', $filters['end_date']);
        }

        // Status filter
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Search filter
        if (isset($filters['search']) && $filters['search']) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Attach products to order.
     */
    public function attachProducts(Order $order, array $products): void
    {
        $productsToSync = [];
        foreach ($products as $productData) {
            $productsToSync[$productData['product_id']] = [
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total_price' => $productData['total_price'],
            ];
        }

        $order->products()->sync($productsToSync);
    }

    /**
     * Detach all products from order.
     */
    public function detachAllProducts(Order $order): void
    {
        $order->products()->detach();
    }

    /**
     * Get orders by status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get orders by date range.
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('order_date', [$startDate, $endDate])->get();
    }
}

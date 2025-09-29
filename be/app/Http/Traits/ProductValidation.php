<?php

namespace App\Http\Traits;

use Illuminate\Validation\ValidationException;

trait ProductValidation
{
    /**
     * Get validation rules for creating a product.
     */
    protected function getProductCreateRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sku' => 'required|string|max:255|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Get validation rules for updating a product.
     */
    protected function getProductUpdateRules(int $productId): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $productId,
            'stock_quantity' => 'sometimes|required|integer|min:0',
        ];
    }

    /**
     * Get validation rules for product filters.
     */
    protected function getProductFilterRules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,low_stock',
            'sort_by' => 'nullable|in:name,price,sku,stock_quantity,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get validation rules for low stock threshold.
     */
    protected function getLowStockThresholdRules(): array
    {
        return [
            'threshold' => 'nullable|integer|min:0|max:1000',
        ];
    }

    /**
     * Validate product creation request.
     * @throws ValidationException
     */
    protected function validateProductCreate($request): void
    {
        $this->validate($request, $this->getProductCreateRules());
    }

    /**
     * Validate product update request.
     * @throws ValidationException
     */
    protected function validateProductUpdate($request, int $productId): void
    {
        $this->validate($request, $this->getProductUpdateRules($productId));
    }

    /**
     * Validate product filter request.
     * @throws ValidationException
     */
    protected function validateProductFilter($request): void
    {
        $this->validate($request, $this->getProductFilterRules());
    }
}

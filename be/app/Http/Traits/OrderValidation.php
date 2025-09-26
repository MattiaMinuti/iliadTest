<?php

namespace App\Http\Traits;

trait OrderValidation
{
    /**
     * Get validation rules for creating an order.
     */
    protected function getOrderCreateRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_date' => 'required|date',
            'status' => 'in:pending,processing,completed,cancelled',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get validation rules for updating an order.
     */
    protected function getOrderUpdateRules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'order_date' => 'sometimes|required|date',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'products' => 'sometimes|array|min:1',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ];
    }

    /**
     * Get validation rules for order filters.
     */
    protected function getOrderFilterRules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'sort_by' => 'nullable|in:name,order_date,total_amount,status,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Validate order creation request.
     */
    protected function validateOrderCreate($request): void
    {
        $this->validate($request, $this->getOrderCreateRules());
    }

    /**
     * Validate order update request.
     */
    protected function validateOrderUpdate($request): void
    {
        $this->validate($request, $this->getOrderUpdateRules());
    }

    /**
     * Validate order filter request.
     */
    protected function validateOrderFilter($request): void
    {
        $this->validate($request, $this->getOrderFilterRules());
    }
}

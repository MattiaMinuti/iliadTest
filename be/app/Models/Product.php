<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the orders that include this product.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    /**
     * Check if product has sufficient stock.
     * This is a simple data accessor, not business logic.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }


    //Used for seeding
    /**
     * Reduce stock quantity for this product.
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->hasStock($quantity)) {
            $this->stock_quantity -= $quantity;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Add stock quantity for this product.
     */
    public function addStock(int $quantity): bool
    {
        $this->stock_quantity += $quantity;
        return $this->save();
    }
}

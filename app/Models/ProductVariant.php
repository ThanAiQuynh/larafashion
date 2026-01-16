<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'color_code',
        'stock_quantity',
        'price_adjustment',
        'sku',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_adjustment' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helpers
    /**
     * Get the final price for this variant
     */
    public function getFinalPrice(): float
    {
        return $this->product->price + $this->price_adjustment;
    }

    /**
     * Check if this variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get display name for this variant
     */
    public function getDisplayName(): string
    {
        $parts = [];
        if ($this->size) {
            $parts[] = "Size: {$this->size}";
        }
        if ($this->color) {
            $parts[] = "Màu: {$this->color}";
        }
        return implode(' - ', $parts) ?: 'Mặc định';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    protected static function booted()
    {
        static::saved(function ($variant) {
            $variant->syncParentStock();
        });

        static::deleted(function ($variant) {
            $variant->syncParentStock();
        });
    }

    public function syncParentStock()
    {
        if ($this->product_id) {
            $totalStock = static::where('product_id', $this->product_id)
                ->where('is_active', true)
                ->sum('stock_quantity');

            $this->product()->update(['stock_quantity' => $totalStock]);
        }
    }
}

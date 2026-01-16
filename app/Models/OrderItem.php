<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'size',
        'color',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get display name with variant info
     */
    public function getDisplayName(): string
    {
        $name = $this->product_name;
        $variant = [];
        if ($this->size) {
            $variant[] = "Size: {$this->size}";
        }
        if ($this->color) {
            $variant[] = "Màu: {$this->color}";
        }
        if (!empty($variant)) {
            $name .= ' (' . implode(', ', $variant) . ')';
        }
        return $name;
    }
}

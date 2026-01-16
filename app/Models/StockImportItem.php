<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockImportItem extends Model
{
    protected $fillable = [
        'stock_import_id',
        'product_id',
        'variant_id',
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

    // Auto calculate total price
    protected static function booted(): void
    {
        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    // Relationships
    public function stockImport(): BelongsTo
    {
        return $this->belongsTo(StockImport::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Get display name
    public function getDisplayName(): string
    {
        $name = $this->product->name;
        if ($this->variant) {
            $parts = [];
            if ($this->variant->size)
                $parts[] = 'Size: ' . $this->variant->size;
            if ($this->variant->color)
                $parts[] = 'Màu: ' . $this->variant->color;
            if (!empty($parts)) {
                $name .= ' (' . implode(', ', $parts) . ')';
            }
        }
        return $name;
    }
}

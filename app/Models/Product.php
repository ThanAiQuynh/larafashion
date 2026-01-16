<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category_id',
        'brand_id',
        'price',
        'original_price',
        'stock_quantity',
        'description',
        'thumbnail_url',
        'images',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get available sizes for this product
     */
    public function getAvailableSizes(): array
    {
        return $this->variants()
            ->whereNotNull('size')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->distinct()
            ->pluck('size')
            ->toArray();
    }

    /**
     * Get available colors for this product
     */
    public function getAvailableColors(): array
    {
        return $this->variants()
            ->whereNotNull('color')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->select('color', 'color_code')
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Check if product has variants
     */
    public function hasVariants(): bool
    {
        return $this->variants()->count() > 0;
    }

    /**
     * Get total stock from all variants or base stock
     */
    public function getTotalStock(): int
    {
        if ($this->hasVariants()) {
            return (int) $this->variants()->where('is_active', true)->sum('stock_quantity');
        }
        return $this->stock_quantity;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where('name', 'like', "%{$keyword}%");
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('stock_quantity', '>', 0)
                ->orWhereHas('variants', function ($v) {
                    $v->where('is_active', true)
                        ->where('stock_quantity', '>', 0);
                });
        });
    }

    // Helpers
    public function getDiscountPercentage(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return null;
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }
}

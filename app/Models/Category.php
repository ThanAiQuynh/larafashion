<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_active',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all products including those in subcategories
     */
    public function allProducts()
    {
        if ($this->parent_id === null) {
            // If it's a parent, get products from itself and all its children
            $categoryIds = $this->children()->pluck('id')->push($this->id);
            return Product::whereIn('category_id', $categoryIds);
        }

        return $this->products();
    }

    public function getTotalProductsCountAttribute(): int
    {
        if ($this->relationLoaded('children') && $this->children->every(fn($child) => $child->relationLoaded('products'))) {
            return $this->products->count() + $this->children->sum(fn($child) => $child->products->count());
        }

        // Fallback to query if relations are not loaded (though we should load them)
        return $this->allProducts()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_count',
        'usage_per_user',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // Check if voucher is valid
    public function isValid(): bool
    {
        if (!$this->is_active)
            return false;
        if (now() < $this->start_date)
            return false;
        if (now() > $this->end_date)
            return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit)
            return false;
        return true;
    }

    // Check if user can use this voucher
    public function canBeUsedBy(?int $userId): bool
    {
        if (!$this->isValid())
            return false;

        if ($userId && $this->usage_per_user > 0) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->usage_per_user)
                return false;
        }

        return true;
    }

    // Calculate discount for order
    public function calculateDiscount(float $orderTotal): float
    {
        if ($orderTotal < $this->min_order_value) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($orderTotal * $this->value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->value;
        }

        // Discount cannot exceed order total
        return min($discount, $orderTotal);
    }

    // Record usage
    public function recordUsage(int $orderId, float $discountAmount, ?int $userId = null): VoucherUsage
    {
        $this->increment('usage_count');

        return $this->usages()->create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'discount_amount' => $discountAmount,
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // Get type label
    public function getTypeLabel(): string
    {
        return $this->type === 'percentage' ? 'Giảm %' : 'Giảm tiền';
    }

    // Get value display
    public function getValueDisplay(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return number_format((float) $this->value, 0, ',', '.') . 'đ';
    }
}

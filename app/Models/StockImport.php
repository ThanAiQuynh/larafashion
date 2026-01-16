<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockImport extends Model
{
    protected $fillable = [
        'code',
        'supplier_id',
        'import_date',
        'total_amount',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'import_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    // Auto generate code
    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $lastImport = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastImport ? (int) substr($lastImport->code, -3) + 1 : 1;
        return 'PN-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockImportItem::class);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // Confirm import - update stock
    public function confirm(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        foreach ($this->items as $item) {
            if ($item->variant_id) {
                // Update variant stock
                $item->variant->increment('stock_quantity', $item->quantity);
            } else {
                // Update product stock
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        $this->update(['status' => 'completed']);
        return true;
    }

    // Cancel import
    public function cancel(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update(['status' => 'cancelled']);
        return true;
    }
}

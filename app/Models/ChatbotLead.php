<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotLead extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tudongchat_session_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'intent',
        'raw_data',
        'is_processed',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'is_processed' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Actions
    public function markAsProcessed(): bool
    {
        return $this->update(['is_processed' => true]);
    }

    /**
     * Create lead from webhook payload
     */
    public static function createFromWebhook(array $payload): self
    {
        return self::create([
            'tudongchat_session_id' => $payload['session_id'] ?? null,
            'customer_name' => $payload['name'] ?? $payload['customer_name'] ?? null,
            'customer_phone' => $payload['phone'] ?? $payload['customer_phone'] ?? null,
            'customer_email' => $payload['email'] ?? $payload['customer_email'] ?? null,
            'intent' => $payload['intent'] ?? $payload['message'] ?? null,
            'raw_data' => $payload,
            'is_processed' => false,
        ]);
    }
}

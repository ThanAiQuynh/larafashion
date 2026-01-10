<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotConfig extends Model
{
    protected $fillable = [
        'script_code',
        'is_active',
        'webhook_secret',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the active chatbot configuration (singleton pattern)
     */
    public static function getConfig(): ?self
    {
        return self::first();
    }

    /**
     * Check if chatbot is enabled
     */
    public static function isEnabled(): bool
    {
        $config = self::getConfig();
        return $config && $config->is_active && !empty($config->script_code);
    }

    /**
     * Validate webhook secret
     */
    public function validateSecret(string $secret): bool
    {
        return $this->webhook_secret && hash_equals($this->webhook_secret, $secret);
    }
}

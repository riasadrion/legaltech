<?php

namespace App\Domain\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gateway extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
            'min_amount' => 'float',
            'max_amount' => 'float',
            'fee_flat' => 'float',
            'fee_percent' => 'float',
            'exchange_rate' => 'float',
            'decimal_places' => 'integer',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getCredentials(): array
    {
        if (! $this->credentials) {
            return [];
        }
        return json_decode($this->credentials, true) ?? [];
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = json_encode($credentials);
    }

    public function credential(string $key, mixed $default = null): mixed
    {
        return $this->getCredentials()[$key] ?? $default;
    }

    /**
     * Calculate the fee for a given amount.
     */
    public function calculateFee(float $amount): float
    {
        return round($this->fee_flat + ($amount * $this->fee_percent / 100), $this->decimal_places);
    }

    /**
     * Format an amount according to this gateway's currency settings.
     */
    public function formatAmount(float $amount): string
    {
        $formatted = number_format($amount, $this->decimal_places);
        
        if ($this->symbol_position === 'before') {
            return $this->currency_symbol . $formatted;
        }
        
        return $formatted . $this->currency_symbol;
    }

    /**
     * Get the currency code for display purposes.
     */
    public function getCurrencyCode(): string
    {
        return $this->currency_code;
    }

    /**
     * Get the currency symbol.
     */
    public function getCurrencySymbol(): string
    {
        return $this->currency_symbol;
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function logoUrl(): ?string
    {
        $path = base_path('../assets/uploads/gateways/' . $this->driver . '.svg');
        if (file_exists($path)) {
            return url('assets/uploads/gateways/' . $this->driver . '.svg');
        }
        return null;
    }
}

<?php

namespace App\Domain\Payment\Models;

use App\Domain\Forms\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutMethod extends Model
{
    protected $fillable = [
        'form_id', 'name', 'delay_text', 'min_amount', 'max_amount',
        'fee_flat', 'fee_percent', 'kyc_required', 'enabled', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'delay_text' => 'array',
            'min_amount' => 'float',
            'max_amount' => 'float',
            'fee_flat' => 'float',
            'fee_percent' => 'float',
            'kyc_required' => 'boolean',
            'enabled' => 'boolean',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true)->orderBy('sort');
    }

    public function getLocaleName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $name = $this->name;

        return $name[$locale] ?? $name['en'] ?? '';
    }

    public function getLocaleDelayText(?string $locale = null): ?string
    {
        if (! $this->delay_text) {
            return null;
        }
        $locale = $locale ?: app()->getLocale();

        return $this->delay_text[$locale] ?? $this->delay_text['en'] ?? null;
    }

    public function calculateFee(float $amount): float
    {
        $flat = $this->fee_flat;
        $percent = round($amount * ($this->fee_percent / 100), 2);

        return round($flat + $percent, 2);
    }
}

<?php

namespace App\Domain\Subscription\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const CYCLE_PER_CONTRACT = 'per_contract';

    const CYCLE_MONTHLY = 'monthly';

    const CYCLE_YEARLY = 'yearly';

    const CYCLE_LIFETIME = 'lifetime';

    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_cycle',
        'credits',
        'trial_days',
        'features',
        'is_featured',
        'cta_text',
        'cta_url',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'float',
        'credits' => 'integer',
        'trial_days' => 'integer',
    ];

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function formattedPrice(): string
    {
        $symbol = setting('site.currency_symbol', '$');
        return $symbol . number_format($this->price, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasTrialPeriod(): bool
    {
        return $this->trial_days > 0;
    }

    public function hasCreditLimit(): bool
    {
        return $this->credits > 0;
    }
}

<?php

namespace App\Domain\Subscription\Models;

use App\Domain\Payment\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    const STATUS_TRIAL = 'trial';

    const STATUS_ACTIVE = 'active';

    const STATUS_PAST_DUE = 'past_due';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'credits_remaining',
        'ai_drafts_remaining',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'payment_id',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'credits_remaining' => 'integer',
        'ai_drafts_remaining' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function creditLogs()
    {
        return $this->hasMany(CreditLog::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIAL]);
    }

    public function isTrialing(): bool
    {
        return $this->status === self::STATUS_TRIAL && $this->trial_ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
    }

    public function daysRemaining(): int
    {
        return (int) now()->diffInDays($this->current_period_end, false);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIAL]);
    }
}

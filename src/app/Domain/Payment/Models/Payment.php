<?php

namespace App\Domain\Payment\Models;

use App\Domain\Wallet\Models\Transaction;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'fee' => 'float',
            'reviewed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    const STATUS_PENDING = 'pending';

    const STATUS_AWAITING_APPROVAL = 'awaiting_approval';

    const STATUS_COMPLETED = 'completed';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function netAmount(): float
    {
        return $this->amount - $this->fee;
    }

    public function formattedAmount(): string
    {
        if ($this->gateway) {
            return $this->gateway->formatAmount($this->amount);
        }

        $symbol = setting('site.currency_symbol', '$');

        return $symbol.number_format($this->amount, 2);
    }

    public function formattedFee(): string
    {
        if ($this->gateway) {
            return $this->gateway->formatAmount($this->fee);
        }

        $symbol = setting('site.currency_symbol', '$');

        return $symbol.number_format($this->fee, 2);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAwaitingApproval(): bool
    {
        return $this->status === self::STATUS_AWAITING_APPROVAL;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', self::STATUS_AWAITING_APPROVAL);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}

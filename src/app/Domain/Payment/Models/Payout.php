<?php

namespace App\Domain\Payment\Models;

use App\Domain\Wallet\Models\Transaction;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $table = 'payouts';
    protected $fillable = [
        'user_id', 'payout_method_id', 'amount', 'currency',
        'fee', 'status', 'payout_details', 'rejection_reason',
        'reviewed_by', 'reviewed_at', 'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'fee' => 'float',
            'payout_details' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function netAmount(): float
    {
        return $this->amount - $this->fee;
    }

    public function formattedAmount(): string
    {
        return formatAmount($this->amount, $this->currency);
    }

    public function formattedFee(): string
    {
        return formatAmount($this->fee, $this->currency);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
}

<?php

namespace App\Domain\Wallet\Models;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    // Immutable ledger — no updates ever
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'balance_after' => 'float',
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Types
    const TYPE_CREDIT = 'credit';

    const TYPE_DEBIT = 'debit';

    const TYPE_ADJUSTMENT = 'adjustment';

    const TYPE_FEE = 'fee';

    const TYPE_REFUND = 'refund';

    const TYPE_TRANSFER_IN = 'transfer_in';

    const TYPE_TRANSFER_OUT = 'transfer_out';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return $this->amount > 0;
    }

    public function isDebit(): bool
    {
        return $this->amount < 0;
    }

    public function formattedAmount(): string
    {
        $symbol = $this->amount >= 0 ? '+' : '';
        return $symbol . formatAmount(abs($this->amount));
    }

    public function formattedBalance(): string
    {
        return formatAmount($this->balance_after);
    }

    // Block any update attempts — ledger rows are immutable
    protected static function booted(): void
    {
        static::updating(function () {
            throw new \LogicException('Transaction records are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \LogicException('Transaction records are immutable and cannot be deleted.');
        });
    }
}

<?php

namespace App\Domain\Wallet\Repositories;

use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\ValueObjects\Money;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    /**
     * Insert a new ledger row. Calculates balance_after inside a row-level lock
     * so concurrent inserts stay consistent.
     */
    public function insert(
        User $user,
        string $type,
        Money $amount,
        string $description,
        array $meta = [],
        ?int $adminId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
    ): Transaction {
        return DB::transaction(function () use (
            $user, $type, $amount, $description, $meta, $adminId, $subjectType, $subjectId
        ) {
            // Lock the user row to serialize concurrent balance writes
            $user = User::lockForUpdate()->findOrFail($user->id);

            $currentBalance = $user->balance ?? 0.0;
            $newBalance = $currentBalance + $amount->amount;

            if ($newBalance < 0) {
                throw new \DomainException('Insufficient balance.');
            }

            $tx = new Transaction;
            $tx->user_id = $user->id;
            $tx->type = $type;
            $tx->amount = $amount->amount;
            $tx->currency = $amount->currency;
            $tx->balance_after = $newBalance;
            $tx->description = $description;
            $tx->meta = $meta ?: null;
            $tx->admin_id = $adminId;
            $tx->subject_type = $subjectType;
            $tx->subject_id = $subjectId;
            $tx->save();

            // Keep denormalized balance in sync
            $user->balance = $newBalance;
            $user->save();

            return $tx;
        });
    }

    /**
     * Sum all transaction amounts for a user — used by reconcile command.
     */
    public function sumForUser(User $user): float
    {
        return (float) Transaction::where('user_id', $user->id)->sum('amount');
    }
}

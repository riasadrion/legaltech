<?php

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Repositories\TransactionRepository;
use App\Domain\Wallet\ValueObjects\Money;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function __construct(
        private TransactionRepository $repo,
    ) {}

    public function credit(
        User $user,
        Money $amount,
        string $description,
        array $meta = [],
        ?int $adminId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
    ): Transaction {
        if (! $amount->isPositive()) {
            throw new \DomainException('Credit amount must be positive.');
        }

        return $this->repo->insert(
            user: $user,
            type: Transaction::TYPE_CREDIT,
            amount: $amount,
            description: $description,
            meta: $meta,
            adminId: $adminId,
            subjectType: $subjectType,
            subjectId: $subjectId,
        );
    }

    public function debit(
        User $user,
        Money $amount,
        string $description,
        array $meta = [],
        ?int $adminId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
    ): Transaction {
        if (! $amount->isPositive()) {
            throw new \DomainException('Debit amount must be positive.');
        }

        return $this->repo->insert(
            user: $user,
            type: Transaction::TYPE_DEBIT,
            amount: $amount->negate(),
            description: $description,
            meta: $meta,
            adminId: $adminId,
            subjectType: $subjectType,
            subjectId: $subjectId,
        );
    }

    /**
     * Admin manual balance adjustment. Positive = add funds, negative = remove funds.
     */
    public function adjust(
        User $user,
        Money $amount,
        string $note,
        Admin $admin,
    ): Transaction {
        if ($amount->isZero()) {
            throw new \DomainException('Adjustment amount cannot be zero.');
        }

        return $this->repo->insert(
            user: $user,
            type: Transaction::TYPE_ADJUSTMENT,
            amount: $amount,
            description: $note ?: 'Manual adjustment by admin',
            meta: ['admin_name' => $admin->name, 'admin_email' => $admin->email],
            adminId: $admin->id,
        );
    }

    /**
     * Transfer between two users.
     */
    public function transfer(
        User $from,
        User $to,
        Money $amount,
        string $description,
    ): array {
        if (! $amount->isPositive()) {
            throw new \DomainException('Transfer amount must be positive.');
        }

        return DB::transaction(function () use ($from, $to, $amount, $description) {
            $out = $this->repo->insert(
                user: $from,
                type: Transaction::TYPE_TRANSFER_OUT,
                amount: $amount->negate(),
                description: $description,
                meta: ['to_user_id' => $to->id, 'to_email' => $to->email],
            );

            $in = $this->repo->insert(
                user: $to,
                type: Transaction::TYPE_TRANSFER_IN,
                amount: $amount,
                description: $description,
                meta: ['from_user_id' => $from->id, 'from_email' => $from->email],
            );

            return [$out, $in];
        });
    }

    public function getBalance(User $user): Money
    {
        return Money::of($user->balance ?? 0.0, 'USD');
    }
}

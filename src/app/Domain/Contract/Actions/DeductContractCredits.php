<?php

namespace App\Domain\Contract\Actions;

use App\Domain\Contract\Models\ContractTemplate;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\ValueObjects\Money;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeductContractCredits
{
    public function __construct(private WalletService $wallet) {}

    public function run(User $user, ContractTemplate $template): int
    {
        $cost = $template->credits_cost;

        if ($cost === 0) {
            return 0;
        }

        return DB::transaction(function () use ($user, $template, $cost) {
            $sub = $user->subscription;

            // Use subscription credits first
            if ($sub && $sub->isActive() && $sub->credits_remaining >= $cost) {
                $sub->decrement('credits_remaining', $cost);
                return $cost;
            }

            // Fall back to wallet balance
            $balance = $this->wallet->getBalance($user);
            if ($balance->amount < $cost) {
                throw new \RuntimeException('Insufficient credits to generate this contract.');
            }

            $this->wallet->debit(
                $user,
                Money::of($cost, $balance->currency),
                "Contract generation: {$template->name}",
            );

            return $cost;
        });
    }
}

<?php

namespace App\Domain\Contract\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeductAiGenerationCredits
{
    public function run(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $sub = $user->subscription;

            if (!$sub || !$sub->isActive()) {
                throw new \RuntimeException('Active subscription required for AI contract generation.');
            }

            if (!$sub->plan?->ai_contract_generation) {
                throw new \RuntimeException('Your current plan does not include AI contract generation.');
            }

            if ($sub->ai_drafts_remaining <= 0) {
                throw new \RuntimeException('You have no AI drafts remaining this period. They will reset on your next billing cycle.');
            }

            $sub->decrement('ai_drafts_remaining', 1);

            return 1;
        });
    }
}

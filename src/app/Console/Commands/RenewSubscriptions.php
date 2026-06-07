<?php

namespace App\Console\Commands;

use App\Domain\Subscription\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscription:renew';

    protected $description = 'Auto-renew active subscriptions whose period has ended and reset credits';

    public function handle(): int
    {
        $renewed = 0;

        UserSubscription::query()
            ->with('plan')
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->where('current_period_end', '<', now())
            ->whereHas('plan', fn ($q) => $q->whereIn('billing_cycle', ['monthly', 'yearly']))
            ->chunk(200, function ($subs) use (&$renewed) {
                foreach ($subs as $sub) {
                    DB::transaction(function () use ($sub, &$renewed) {
                        $plan = $sub->plan;

                        $newEnd = match ($plan->billing_cycle) {
                            'monthly' => $sub->current_period_end->addMonth(),
                            'yearly' => $sub->current_period_end->addYear(),
                            default => null,
                        };

                        if (! $newEnd) {
                            return;
                        }

                        $sub->current_period_start = $sub->current_period_end;
                        $sub->current_period_end = $newEnd;
                        $sub->credits_remaining = $plan->credits;
                        $sub->ai_drafts_remaining = $plan->ai_contract_generation ? (int) $plan->ai_drafts : 0;
                        $sub->save();

                        $renewed++;
                    });
                }
            });

        $this->info("Renewed {$renewed} subscription(s).");

        return 0;
    }
}

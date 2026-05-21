<?php

namespace App\Domain\Subscription;

use App\Domain\Payment\Models\Payment;
use App\Domain\Subscription\Models\CreditLog;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\Models\UserSubscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function subscribe(User $user, Plan $plan, ?Payment $payment = null): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan, $payment) {
            $existing = UserSubscription::where('user_id', $user->id)
                ->active()
                ->first();

            if ($existing) {
                $this->cancel($existing);
            }

            $periodEnd = match ($plan->billing_cycle) {
                Plan::CYCLE_MONTHLY => now()->addMonth(),
                Plan::CYCLE_YEARLY => now()->addYear(),
                Plan::CYCLE_LIFETIME => now()->addYears(100),
            };

            $sub = new UserSubscription;
            $sub->user_id = $user->id;
            $sub->plan_id = $plan->id;
            $sub->status = UserSubscription::STATUS_ACTIVE;
            $sub->credits_remaining = $plan->credits;
            $sub->current_period_start = now();
            $sub->current_period_end = $periodEnd;
            $sub->payment_id = $payment?->id;
            $sub->save();

            if ($plan->credits > 0) {
                $this->logCredit($user, $sub, $plan->credits, "Plan activation: {$plan->name}");
            }

            return $sub;
        });
    }

    public function startTrial(User $user, Plan $plan): UserSubscription
    {
        if (! $plan->hasTrialPeriod()) {
            throw new \DomainException('Plan does not have a trial period');
        }

        $existing = UserSubscription::where('user_id', $user->id)
            ->active()
            ->first();

        if ($existing) {
            throw new \DomainException('User already has an active subscription');
        }

        $trialEnd = now()->addDays($plan->trial_days);

        $sub = new UserSubscription;
        $sub->user_id = $user->id;
        $sub->plan_id = $plan->id;
        $sub->status = UserSubscription::STATUS_TRIAL;
        $sub->trial_ends_at = $trialEnd;
        $sub->current_period_start = now();
        $sub->current_period_end = $trialEnd;
        $sub->credits_remaining = $plan->credits;
        $sub->save();

        if ($plan->credits > 0) {
            $this->logCredit($user, $sub, $plan->credits, "Trial started: {$plan->name}");
        }

        return $sub;
    }

    public function cancel(UserSubscription $sub): void
    {
        $sub->status = UserSubscription::STATUS_CANCELLED;
        $sub->cancelled_at = now();
        $sub->save();
    }

    public function deductCredits(User $user, int $amount, string $description): bool
    {
        $sub = UserSubscription::where('user_id', $user->id)
            ->active()
            ->first();

        if (! $sub || $sub->credits_remaining < $amount) {
            return false;
        }

        $sub->credits_remaining -= $amount;
        $sub->save();

        $this->logCredit($user, $sub, -$amount, $description);

        return true;
    }

    public function expireOverdue(): void
    {
        UserSubscription::query()
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIAL])
            ->where('current_period_end', '<', now())
            ->chunk(200, function ($subs) {
                foreach ($subs as $sub) {
                    $sub->status = UserSubscription::STATUS_EXPIRED;
                    $sub->save();
                }
            });
    }

    private function logCredit(User $user, ?UserSubscription $sub, int $amount, string $description): void
    {
        $log = new CreditLog;
        $log->user_id = $user->id;
        $log->subscription_id = $sub?->id;
        $log->amount = $amount;
        $log->description = $description;
        $log->save();
    }
}

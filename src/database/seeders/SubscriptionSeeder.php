<?php

namespace Database\Seeders;

use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\Models\UserSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plans = Plan::all()->keyBy('name');

        if ($plans->isEmpty()) {
            $this->command->warn('No plans found — run PlanSeeder first.');
            return;
        }

        $starter      = $plans->get('Starter Monthly');
        $studio       = $plans->get('Studio Monthly');
        $starterYearly = $plans->get('Starter Yearly');
        $studioYearly  = $plans->get('Studio Yearly');

        $subscriptions = [

            // Active · Starter Monthly
            'alice@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(12),
                'current_period_end'   => now()->addDays(18),
            ],

            // Active · Studio Monthly
            'bob@example.com' => [
                'plan'                 => $studio,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(5),
                'current_period_end'   => now()->addDays(25),
            ],

            // Cancelled · Starter Monthly
            'emma@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_CANCELLED,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
                'cancelled_at'         => now()->subDays(10),
            ],

            // Expired · Starter Monthly
            'frank@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_EXPIRED,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(60),
                'current_period_end'   => now()->subDays(30),
            ],

            // Past due · Studio Monthly
            'grace@example.com' => [
                'plan'                 => $studio,
                'status'               => UserSubscription::STATUS_PAST_DUE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
            ],

            // Active · Studio Yearly
            'jack@example.com' => [
                'plan'                 => $studioYearly,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subMonths(3),
                'current_period_end'   => now()->addMonths(9),
            ],

            // Active · Starter Yearly
            'kate@example.com' => [
                'plan'                 => $starterYearly,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subMonths(1),
                'current_period_end'   => now()->addMonths(11),
            ],

            // Active · Studio Monthly (renewing soon)
            'nathan@example.com' => [
                'plan'                 => $studio,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(28),
                'current_period_end'   => now()->addDays(2),
            ],
        ];

        foreach ($subscriptions as $email => $def) {
            $user = User::where('email', $email)->first();

            if (! $user || ! $def['plan']) {
                continue;
            }

            UserSubscription::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id'              => $def['plan']->id,
                    'status'               => $def['status'],
                    'credits_remaining'    => $def['credits_remaining'],
                    'current_period_start' => $def['current_period_start'],
                    'current_period_end'   => $def['current_period_end'],
                    'cancelled_at'         => $def['cancelled_at'] ?? null,
                ]
            );
        }
    }
}

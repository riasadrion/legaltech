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

        $starter         = $plans->get('Starter');
        $pro             = $plans->get('Pro');
        $proYearly       = $plans->get('Pro Yearly');
        $business        = $plans->get('Business');
        $businessYearly  = $plans->get('Business Yearly');
        $enterprise      = $plans->get('Enterprise Yearly');

        $subscriptions = [

            // Active · Starter (free)
            'alice@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 2,
                'ai_drafts_remaining'  => 0,
                'current_period_start' => now()->subDays(12),
                'current_period_end'   => now()->addDays(18),
            ],

            // Active · Business Monthly
            'bob@example.com' => [
                'plan'                 => $business,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 210,
                'ai_drafts_remaining'  => 44,
                'current_period_start' => now()->subDays(5),
                'current_period_end'   => now()->addDays(25),
            ],

            // Cancelled · Pro Monthly
            'emma@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_CANCELLED,
                'credits_remaining'    => 0,
                'ai_drafts_remaining'  => 0,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
                'cancelled_at'         => now()->subDays(10),
            ],

            // Expired · Starter
            'frank@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_EXPIRED,
                'credits_remaining'    => 0,
                'ai_drafts_remaining'  => 0,
                'current_period_start' => now()->subDays(60),
                'current_period_end'   => now()->subDays(30),
            ],

            // Past due · Pro Monthly
            'grace@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_PAST_DUE,
                'credits_remaining'    => 15,
                'ai_drafts_remaining'  => 3,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
            ],

            // Active · Business Yearly
            'jack@example.com' => [
                'plan'                 => $businessYearly,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 2400,
                'ai_drafts_remaining'  => 520,
                'current_period_start' => now()->subMonths(3),
                'current_period_end'   => now()->addMonths(9),
            ],

            // Active · Pro Yearly
            'kate@example.com' => [
                'plan'                 => $proYearly,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 540,
                'ai_drafts_remaining'  => 105,
                'current_period_start' => now()->subMonths(1),
                'current_period_end'   => now()->addMonths(11),
            ],

            // Active · Pro Monthly (renewing soon)
            'nathan@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 8,
                'ai_drafts_remaining'  => 1,
                'current_period_start' => now()->subDays(28),
                'current_period_end'   => now()->addDays(2),
            ],

            // Active · Enterprise Yearly · Test user (demo account)
            'testuser@example.com' => [
                'plan'                 => $enterprise,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 0,
                'ai_drafts_remaining'  => 0,
                'current_period_start' => now()->subDays(8),
                'current_period_end'   => now()->addDays(22),
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
                    'ai_drafts_remaining'  => $def['ai_drafts_remaining'],
                    'current_period_start' => $def['current_period_start'],
                    'current_period_end'   => $def['current_period_end'],
                    'cancelled_at'         => $def['cancelled_at'] ?? null,
                ]
            );
        }
    }
}

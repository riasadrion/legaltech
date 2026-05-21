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

        $starter      = $plans->get('Starter');
        $pro          = $plans->get('Professional');
        $business     = $plans->get('Business');
        $starterAnnual = $plans->get('Starter Annual');
        $proAnnual    = $plans->get('Professional Annual');
        $lifetime     = $plans->get('Lifetime');

        // Map email → subscription definition
        $subscriptions = [

            // Active · Professional monthly
            'alice@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 3200,
                'current_period_start' => now()->subDays(12),
                'current_period_end'   => now()->addDays(18),
            ],

            // Active · Business monthly
            'bob@example.com' => [
                'plan'                 => $business,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 41000,
                'current_period_start' => now()->subDays(5),
                'current_period_end'   => now()->addDays(25),
            ],

            // Trial · Starter
            'carol@example.com' => [
                'plan'              => $starter,
                'status'            => UserSubscription::STATUS_TRIAL,
                'credits_remaining' => 100,
                'trial_ends_at'     => now()->addDays(8),
                'current_period_start' => now()->subDays(6),
                'current_period_end'   => now()->addDays(8),
            ],

            // Trial · Professional (trial expiring soon — 1 day left)
            'david@example.com' => [
                'plan'              => $pro,
                'status'            => UserSubscription::STATUS_TRIAL,
                'credits_remaining' => 5000,
                'trial_ends_at'     => now()->addDay(),
                'current_period_start' => now()->subDays(6),
                'current_period_end'   => now()->addDay(),
            ],

            // Cancelled · Professional
            'emma@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_CANCELLED,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
                'cancelled_at'         => now()->subDays(10),
            ],

            // Expired · Starter
            'frank@example.com' => [
                'plan'                 => $starter,
                'status'               => UserSubscription::STATUS_EXPIRED,
                'credits_remaining'    => 0,
                'current_period_start' => now()->subDays(60),
                'current_period_end'   => now()->subDays(30),
            ],

            // Past due · Professional
            'grace@example.com' => [
                'plan'                 => $pro,
                'status'               => UserSubscription::STATUS_PAST_DUE,
                'credits_remaining'    => 1200,
                'current_period_start' => now()->subDays(35),
                'current_period_end'   => now()->subDays(5),
            ],

            // Active · Professional Annual
            'jack@example.com' => [
                'plan'                 => $proAnnual,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 52000,
                'current_period_start' => now()->subMonths(3),
                'current_period_end'   => now()->addMonths(9),
            ],

            // Active · Starter Annual
            'kate@example.com' => [
                'plan'                 => $starterAnnual,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 900,
                'current_period_start' => now()->subMonths(1),
                'current_period_end'   => now()->addMonths(11),
            ],

            // Active · Lifetime
            'leo@example.com' => [
                'plan'                 => $lifetime,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 999999,
                'current_period_start' => now()->subYear(),
                'current_period_end'   => now()->addYears(10),
            ],

            // Active · Business (renewing soon — 2 days left)
            'nathan@example.com' => [
                'plan'                 => $business,
                'status'               => UserSubscription::STATUS_ACTIVE,
                'credits_remaining'    => 800,
                'current_period_start' => now()->subDays(28),
                'current_period_end'   => now()->addDays(2),
            ],

            // Trial · Professional Annual (just started)
            'olivia@example.com' => [
                'plan'              => $proAnnual,
                'status'            => UserSubscription::STATUS_TRIAL,
                'credits_remaining' => 60000,
                'trial_ends_at'     => now()->addDays(7),
                'current_period_start' => now(),
                'current_period_end'   => now()->addDays(7),
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
                    'trial_ends_at'        => $def['trial_ends_at'] ?? null,
                    'current_period_start' => $def['current_period_start'],
                    'current_period_end'   => $def['current_period_end'],
                    'cancelled_at'         => $def['cancelled_at'] ?? null,
                ]
            );
        }
    }
}

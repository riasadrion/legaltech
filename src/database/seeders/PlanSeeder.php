<?php

namespace Database\Seeders;

use App\Domain\Subscription\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for getting started',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'credits' => 100,
                'trial_days' => 14,
                'features' => [
                    'Up to 100 requests/month',
                    'Email support',
                    '14-day free trial',
                    'Basic analytics',
                ],
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Professional',
                'description' => 'For growing teams',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'credits' => 5000,
                'trial_days' => 7,
                'features' => [
                    'Up to 5,000 requests/month',
                    'Priority email & chat support',
                    '7-day free trial',
                    'Advanced analytics',
                    'API access',
                    'Webhooks',
                ],
                'is_active' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Business',
                'description' => 'For enterprises',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'credits' => 50000,
                'trial_days' => 0,
                'features' => [
                    'Unlimited requests',
                    'Phone & dedicated support',
                    'Custom analytics',
                    'Advanced API features',
                    'Webhooks & integrations',
                    'SLA guarantee',
                    'Custom billing',
                ],
                'is_active' => true,
                'sort' => 3,
            ],
            [
                'name' => 'Starter Annual',
                'description' => 'Annual plan with 20% savings',
                'price' => 239.88,
                'billing_cycle' => 'yearly',
                'credits' => 1200,
                'trial_days' => 14,
                'features' => [
                    'Up to 100 requests/month',
                    'Email support',
                    'Save 20% vs monthly',
                    'Basic analytics',
                ],
                'is_active' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Professional Annual',
                'description' => 'Annual plan with 20% savings',
                'price' => 299.99,
                'billing_cycle' => 'yearly',
                'credits' => 60000,
                'trial_days' => 7,
                'features' => [
                    'Up to 5,000 requests/month',
                    'Priority support',
                    'Save 20% vs monthly',
                    'Advanced analytics',
                    'API access',
                ],
                'is_active' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Lifetime',
                'description' => 'One-time payment, forever access',
                'price' => 999.99,
                'billing_cycle' => 'lifetime',
                'credits' => 999999,
                'trial_days' => 0,
                'features' => [
                    'Unlimited requests forever',
                    'Priority support',
                    'All features included',
                    'Lifetime updates',
                    'Advanced analytics',
                    'No recurring charges',
                ],
                'is_active' => true,
                'sort' => 6,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}

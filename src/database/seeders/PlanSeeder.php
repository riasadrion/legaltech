<?php

namespace Database\Seeders;

use App\Domain\Subscription\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [

            // ── Starter ──────────────────────────────────────────────
            [
                'name'          => 'Starter',
                'description'   => 'For founders & freelancers shipping a few deals.',
                'price'         => 19.00,
                'billing_cycle' => 'per_contract',
                'credits'       => 1,
                'trial_days'    => 0,
                'is_featured'   => false,
                'cta_text'      => 'Start free trial',
                'cta_url'       => '/register',
                'features'      => [
                    'AI contract generation',
                    '2 signers per document',
                    '30+ starter templates',
                    'Standard e-signature (eIDAS Advanced)',
                    'Email support',
                ],
                'is_active' => true,
                'sort'      => 1,
            ],
            [
                'name'          => 'Starter Monthly',
                'description'   => 'For founders & freelancers shipping a few deals.',
                'price'         => 29.00,
                'billing_cycle' => 'monthly',
                'credits'       => 0,
                'trial_days'    => 14,
                'is_featured'   => false,
                'cta_text'      => 'Start free trial',
                'cta_url'       => '/register',
                'features'      => [
                    'AI contract generation',
                    '2 signers per document',
                    '30+ starter templates',
                    'Standard e-signature (eIDAS Advanced)',
                    'Email support',
                ],
                'is_active' => true,
                'sort'      => 2,
            ],
            [
                'name'          => 'Starter Yearly',
                'description'   => 'For founders & freelancers shipping a few deals.',
                'price'         => 278.00,
                'billing_cycle' => 'yearly',
                'credits'       => 0,
                'trial_days'    => 14,
                'is_featured'   => false,
                'cta_text'      => 'Start free trial',
                'cta_url'       => '/register',
                'features'      => [
                    'AI contract generation',
                    '2 signers per document',
                    '30+ starter templates',
                    'Standard e-signature (eIDAS Advanced)',
                    'Email support',
                    'Save 20% vs monthly',
                ],
                'is_active' => true,
                'sort'      => 3,
            ],

            // ── Studio (featured) ─────────────────────────────────────
            [
                'name'          => 'Studio',
                'description'   => 'For growing teams running contracts as a workflow.',
                'price'         => 14.00,
                'billing_cycle' => 'per_contract',
                'credits'       => 1,
                'trial_days'    => 0,
                'is_featured'   => true,
                'cta_text'      => 'Start 14-day trial',
                'cta_url'       => '/register',
                'features'      => [
                    'Everything in Starter',
                    'Unlimited drafts & redlines',
                    'Up to 25 signers per document',
                    'Custom clause library & playbooks',
                    'Qualified eIDAS signatures',
                    'Slack & Notion integrations',
                ],
                'is_active' => true,
                'sort'      => 4,
            ],
            [
                'name'          => 'Studio Monthly',
                'description'   => 'For growing teams running contracts as a workflow.',
                'price'         => 89.00,
                'billing_cycle' => 'monthly',
                'credits'       => 0,
                'trial_days'    => 14,
                'is_featured'   => true,
                'cta_text'      => 'Start 14-day trial',
                'cta_url'       => '/register',
                'features'      => [
                    'Everything in Starter',
                    'Unlimited drafts & redlines',
                    'Up to 25 signers per document',
                    'Custom clause library & playbooks',
                    'Qualified eIDAS signatures',
                    'Slack & Notion integrations',
                ],
                'is_active' => true,
                'sort'      => 5,
            ],
            [
                'name'          => 'Studio Yearly',
                'description'   => 'For growing teams running contracts as a workflow.',
                'price'         => 854.00,
                'billing_cycle' => 'yearly',
                'credits'       => 0,
                'trial_days'    => 14,
                'is_featured'   => true,
                'cta_text'      => 'Start 14-day trial',
                'cta_url'       => '/register',
                'features'      => [
                    'Everything in Starter',
                    'Unlimited drafts & redlines',
                    'Up to 25 signers per document',
                    'Custom clause library & playbooks',
                    'Qualified eIDAS signatures',
                    'Slack & Notion integrations',
                    'Save 20% vs monthly',
                ],
                'is_active' => true,
                'sort'      => 6,
            ],

            // ── Firm (enterprise) ─────────────────────────────────────
            [
                'name'          => 'Firm',
                'description'   => 'For legal departments & agencies at scale.',
                'price'         => 0.00,
                'billing_cycle' => 'per_contract',
                'credits'       => 0,
                'trial_days'    => 0,
                'is_featured'   => false,
                'cta_text'      => 'Talk to sales',
                'cta_url'       => '/contact',
                'features'      => [
                    'Everything in Studio',
                    'SSO · SCIM · region pinning',
                    'Dedicated drafting model',
                    'Custom DPA & security review',
                    'Named CSM & 24/5 support',
                ],
                'is_active' => true,
                'sort'      => 7,
            ],
            [
                'name'          => 'Firm Monthly',
                'description'   => 'For legal departments & agencies at scale.',
                'price'         => 0.00,
                'billing_cycle' => 'monthly',
                'credits'       => 0,
                'trial_days'    => 0,
                'is_featured'   => false,
                'cta_text'      => 'Talk to sales',
                'cta_url'       => '/contact',
                'features'      => [
                    'Everything in Studio',
                    'SSO · SCIM · region pinning',
                    'Dedicated drafting model',
                    'Custom DPA & security review',
                    'Named CSM & 24/5 support',
                ],
                'is_active' => true,
                'sort'      => 8,
            ],
            [
                'name'          => 'Firm Yearly',
                'description'   => 'For legal departments & agencies at scale.',
                'price'         => 0.00,
                'billing_cycle' => 'yearly',
                'credits'       => 0,
                'trial_days'    => 0,
                'is_featured'   => false,
                'cta_text'      => 'Talk to sales',
                'cta_url'       => '/contact',
                'features'      => [
                    'Everything in Studio',
                    'SSO · SCIM · region pinning',
                    'Dedicated drafting model',
                    'Custom DPA & security review',
                    'Named CSM & 24/5 support',
                ],
                'is_active' => true,
                'sort'      => 9,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}

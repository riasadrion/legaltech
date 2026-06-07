<?php

namespace Database\Seeders;

use App\Domain\Subscription\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [

            // ── Pay as you go ─────────────────────────────────────────────
            [
                'name' => 'Pay as you go',
                'description' => 'No subscription. Pay only for what you use.',
                'price' => 9.00,
                'billing_cycle' => 'per_contract',
                'credits' => 1,
                'ai_contract_generation' => false,
                'ai_drafts' => 0,
                'is_featured' => false,
                'cta_text' => 'Get started',
                'cta_url' => '/register',
                'features' => [
                    '1 contract per purchase',
                    'All template categories',
                    'PDF export',
                    'E-signature requests',
                ],
                'is_active' => true,
                'sort' => 0,
            ],

            // ── Starter — Free ────────────────────────────────────────────
            [
                'name' => 'Starter',
                'description' => 'For individuals trying things out.',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'credits' => 3,
                'ai_contract_generation' => false,
                'ai_drafts' => 0,
                'is_featured' => false,
                'cta_text' => 'Start free',
                'cta_url' => '/register',
                'features' => [
                    '3 contract generations',
                    'All template categories',
                    'PDF export',
                    'E-signature requests',
                ],
                'is_active' => true,
                'sort' => 1,
            ],

            // ── Pro — Monthly ─────────────────────────────────────────────
            [
                'name' => 'Pro',
                'description' => 'For freelancers and small teams.',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'credits' => 50,
                'ai_contract_generation' => true,
                'ai_drafts' => 10,
                'is_featured' => true,
                'cta_text' => 'Subscribe now',
                'cta_url' => '/register',
                'features' => [
                    '10 AI drafts',
                    '50 contract generations',
                    'Priority support',
                    'Unlimited templates',
                ],
                'is_active' => true,
                'sort' => 2,
            ],

            // ── Pro — Yearly ──────────────────────────────────────────────
            [
                'name' => 'Pro Yearly',
                'description' => 'Pro plan billed annually — save 20%.',
                'price' => 279.00,
                'billing_cycle' => 'yearly',
                'credits' => 600,
                'ai_contract_generation' => true,
                'ai_drafts' => 120,
                'is_featured' => false,
                'cta_text' => 'Subscribe now',
                'cta_url' => '/register',
                'features' => [
                    '120 AI drafts',
                    '600 contract generations',
                    'Priority support',
                    '2 months free',
                ],
                'is_active' => true,
                'sort' => 3,
            ],

            // ── Business — Monthly ────────────────────────────────────────
            [
                'name' => 'Business',
                'description' => 'For growing companies.',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'credits' => 250,
                'ai_contract_generation' => true,
                'ai_drafts' => 50,
                'is_featured' => false,
                'cta_text' => 'Subscribe now',
                'cta_url' => '/register',
                'features' => [
                    '50 AI drafts',
                    '250 contract generations',
                    'Team seats',
                    'API access',
                    'Dedicated support',
                ],
                'is_active' => true,
                'sort' => 4,
            ],

            // ── Business — Yearly ─────────────────────────────────────────
            [
                'name' => 'Business Yearly',
                'description' => 'Business plan billed annually — save 20%.',
                'price' => 950.00,
                'billing_cycle' => 'yearly',
                'credits' => 3000,
                'ai_contract_generation' => true,
                'ai_drafts' => 600,
                'is_featured' => false,
                'cta_text' => 'Subscribe now',
                'cta_url' => '/register',
                'features' => [
                    '600 AI drafts',
                    '3,000 contract generations',
                    'Team seats',
                    'API access',
                    'Dedicated support',
                    '2 months free',
                ],
                'is_active' => true,
                'sort' => 5,
            ],

            // ── Enterprise — Yearly ───────────────────────────────────────
            [
                'name' => 'Enterprise Yearly',
                'description' => 'Custom pricing for legal teams and large orgs.',
                'price' => 0.00,
                'billing_cycle' => 'yearly',
                'credits' => 0,
                'ai_contract_generation' => true,
                'ai_drafts' => 0,
                'is_featured' => true,
                'cta_text' => 'Talk to sales',
                'cta_url' => '/contact',
                'features' => [
                    'Unlimited AI drafts',
                    'Unlimited contract generations',
                    'SSO & SAML',
                    'Dedicated account manager',
                    'Custom integrations',
                    'SLA',
                ],
                'is_active' => true,
                'sort' => 7,
            ],
        ];

        foreach ($plans as $data) {
            Plan::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}

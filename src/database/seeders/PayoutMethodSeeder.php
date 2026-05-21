<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\PayoutMethod;
use Illuminate\Database\Seeder;

class PayoutMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => ['en' => 'Bank Transfer', 'fr' => 'Virement bancaire'],
                'delay_text' => ['en' => '3-5 business days', 'fr' => '3-5 jours ouvrables'],
                'min_amount' => 10.00,
                'max_amount' => 999999.99,
                'fee_flat' => 2.50,
                'fee_percent' => 0.5,
                'kyc_required' => true,
                'enabled' => true,
                'sort' => 1,
            ],
            [
                'name' => ['en' => 'PayPal', 'fr' => 'PayPal'],
                'delay_text' => ['en' => '1-2 business days', 'fr' => '1-2 jours ouvrables'],
                'min_amount' => 5.00,
                'max_amount' => 100000.00,
                'fee_flat' => 0.99,
                'fee_percent' => 2.2,
                'kyc_required' => false,
                'enabled' => true,
                'sort' => 2,
            ],
            [
                'name' => ['en' => 'Stripe Connect', 'fr' => 'Stripe Connect'],
                'delay_text' => ['en' => '1-3 business days', 'fr' => '1-3 jours ouvrables'],
                'min_amount' => 1.00,
                'max_amount' => 999999.99,
                'fee_flat' => 0.25,
                'fee_percent' => 0.25,
                'kyc_required' => true,
                'enabled' => true,
                'sort' => 3,
            ],
            [
                'name' => ['en' => 'Mobile Money', 'fr' => 'Porte-monnaie mobile'],
                'delay_text' => ['en' => 'Instant - 1 hour', 'fr' => 'Instantané - 1 heure'],
                'min_amount' => 1.00,
                'max_amount' => 5000.00,
                'fee_flat' => 0.00,
                'fee_percent' => 1.0,
                'kyc_required' => false,
                'enabled' => true,
                'sort' => 4,
            ],
            [
                'name' => ['en' => 'Cryptocurrency (BTC)', 'fr' => 'Cryptomonnaie (BTC)'],
                'delay_text' => ['en' => '10-30 minutes (1 confirmation)', 'fr' => '10-30 minutes (1 confirmation)'],
                'min_amount' => 0.0001,
                'max_amount' => 10.0,
                'fee_flat' => 0.00,
                'fee_percent' => 1.0,
                'kyc_required' => false,
                'enabled' => true,
                'sort' => 5,
            ],
            [
                'name' => ['en' => 'Cryptocurrency (ETH)', 'fr' => 'Cryptomonnaie (ETH)'],
                'delay_text' => ['en' => '15-60 minutes (12 confirmations)', 'fr' => '15-60 minutes (12 confirmations)'],
                'min_amount' => 0.001,
                'max_amount' => 100.0,
                'fee_flat' => 0.00,
                'fee_percent' => 1.5,
                'kyc_required' => false,
                'enabled' => true,
                'sort' => 6,
            ],
            [
                'name' => ['en' => 'Check by Mail', 'fr' => 'Chèque par courrier'],
                'delay_text' => ['en' => '5-10 business days', 'fr' => '5-10 jours ouvrables'],
                'min_amount' => 50.00,
                'max_amount' => 10000.00,
                'fee_flat' => 5.00,
                'fee_percent' => 0.0,
                'kyc_required' => true,
                'enabled' => false,
                'sort' => 7,
            ],
        ];

        foreach ($methods as $method) {
            PayoutMethod::firstOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}

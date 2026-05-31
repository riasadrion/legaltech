<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\Gateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'driver' => 'stripe',
                'name' => 'Stripe',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 2,
                'min_amount' => 0.50,
                'max_amount' => 999999.99,
                'fee_flat' => 0.30,
                'fee_percent' => 2.9,
                'enabled' => true,
                'config' => [],
            ],
            [
                'driver' => 'paypal',
                'name' => 'PayPal',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 2,
                'min_amount' => 1.00,
                'max_amount' => 999999.99,
                'fee_flat' => 0.49,
                'fee_percent' => 3.49,
                'enabled' => true,
                'config' => ['sandbox' => true],
            ],
            [
                'driver' => 'razorpay',
                'name' => 'Razorpay',
                'currency_code' => 'INR',
                'currency_symbol' => '₹',
                'symbol_position' => 'before',
                'exchange_rate' => 83.12,
                'decimal_places' => 2,
                'min_amount' => 10.00,
                'max_amount' => 9999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 2.36,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'paystack',
                'name' => 'Paystack',
                'currency_code' => 'NGN',
                'currency_symbol' => '₦',
                'symbol_position' => 'before',
                'exchange_rate' => 770.50,
                'decimal_places' => 2,
                'min_amount' => 50.00,
                'max_amount' => 9999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 1.5,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'flutterwave',
                'name' => 'Flutterwave',
                'currency_code' => 'NGN',
                'currency_symbol' => '₦',
                'symbol_position' => 'before',
                'exchange_rate' => 770.50,
                'decimal_places' => 2,
                'min_amount' => 100.00,
                'max_amount' => 9999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 1.4,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'mollie',
                'name' => 'Mollie',
                'currency_code' => 'EUR',
                'currency_symbol' => '€',
                'symbol_position' => 'after',
                'exchange_rate' => 0.92,
                'decimal_places' => 2,
                'min_amount' => 0.01,
                'max_amount' => 999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 3.5,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'mercadopago',
                'name' => 'Mercado Pago',
                'currency_code' => 'ARS',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 835.50,
                'decimal_places' => 2,
                'min_amount' => 1.00,
                'max_amount' => 999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 2.9,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'coinbase_commerce',
                'name' => 'Coinbase Commerce',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 6,
                'min_amount' => 0.000001,
                'max_amount' => 999999.999999,
                'fee_flat' => 0.00,
                'fee_percent' => 1.0,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'coingate',
                'name' => 'CoinGate',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 8,
                'min_amount' => 0.00000001,
                'max_amount' => 999999.99999999,
                'fee_flat' => 0.00,
                'fee_percent' => 1.0,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'nowpayments',
                'name' => 'NOWPayments',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 8,
                'min_amount' => 0.00000001,
                'max_amount' => 999999.99999999,
                'fee_flat' => 0.00,
                'fee_percent' => 0.5,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'izipay',
                'name' => 'Izipay',
                'currency_code' => 'XOF',
                'currency_symbol' => 'CFA',
                'symbol_position' => 'after',
                'exchange_rate' => 609.50,
                'decimal_places' => 2,
                'min_amount' => 100.00,
                'max_amount' => 9999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 1.5,
                'enabled' => false,
                'config' => [],
            ],
            [
                'driver' => 'manual',
                'name' => 'Manual Payment',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'symbol_position' => 'before',
                'exchange_rate' => 1.0,
                'decimal_places' => 2,
                'min_amount' => 0.01,
                'max_amount' => 999999.99,
                'fee_flat' => 0.00,
                'fee_percent' => 0.00,
                'enabled' => false,
                'config' => [],
            ],
        ];

        foreach ($gateways as $gateway) {
            Gateway::updateOrCreate(
                ['driver' => $gateway['driver']],
                $gateway
            );
        }

        // Set PayPal credentials
        $paypal = Gateway::where('driver', 'paypal')->first();
        if ($paypal) {
            $paypal->setCredentials([
                'business_email' => 'sb-ocy7v2748231@business.example.com',
            ]);
            $paypal->save();
        }

        // Set Stripe credentials
        $stripe = Gateway::where('driver', 'stripe')->first();
        if ($stripe) {
            $stripe->setCredentials([
                'public_key'     => 'pk_test_51Qgl5hEXJkqlhE5dvoJILmWZkxP2c9d3dZpOvxPJe7L84GZWcKtTDwDxUwA3GWK091oyv8srzZrPk8zSDpNh9XIQ00Eow9F1BH',
                'secret_key'     => 'sk_test_51Qgl5hEXJkqlhE5dd0kHNboynWv0qiHIQhxwi8Yy1HiJ797cyczL9g81VBoN2ErsXlCKS5OAgJcmyJHgsTm3x5Ds0096ta4IjO',
                'webhook_secret' => 'whsec_bLQgFKEQQ3GBNHHV0vpMnl3AjQW0zgqQ',
            ]);
            $stripe->save();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use App\Domain\Wallet\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(15)->get();
        $stripeGateway = Gateway::where('driver', 'stripe')->first();
        $paypalGateway = Gateway::where('driver', 'paypal')->first();

        if ($users->isEmpty() || ! $stripeGateway) {
            return;
        }

        $gateways = collect(array_filter([$stripeGateway, $paypalGateway]));

        // Monthly revenue targets — gradual growth over 12 months
        $monthlyTargets = [
            -11 => 320,
            -10 => 410,
            -9  => 380,
            -8  => 520,
            -7  => 490,
            -6  => 610,
            -5  => 580,
            -4  => 720,
            -3  => 850,
            -2  => 940,
            -1  => 1180,
             0  => 1420,
        ];

        $amounts = [19.99, 29.99, 49.99, 59.99, 99.99, 149.99, 199.99];

        foreach ($monthlyTargets as $monthOffset => $target) {
            $date = now()->startOfMonth()->addMonths($monthOffset);
            $seeded = 0;

            while ($seeded < $target) {
                $user = $users->random();
                $gateway = $gateways->random();
                $amount = $amounts[array_rand($amounts)];
                $fee = $gateway->calculateFee($amount);
                $net = $amount - $fee;

                // Random day within that month
                $daysInMonth = $date->daysInMonth;
                $createdAt = $date->copy()->addDays(rand(0, $daysInMonth - 1))->addHours(rand(0, 23))->addMinutes(rand(0, 59));

                $payment = Payment::create([
                    'user_id'     => $user->id,
                    'gateway_id'  => $gateway->id,
                    'amount'      => $amount,
                    'currency'    => 'USD',
                    'fee'         => $fee,
                    'status'      => Payment::STATUS_COMPLETED,
                    'gateway_ref' => 'ref_' . uniqid(),
                    'meta'        => ['demo' => true],
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);

                $currentBalance = (float) ($user->balance ?? 0);
                $newBalance = $currentBalance + $net;

                Transaction::create([
                    'user_id'      => $user->id,
                    'type'         => Transaction::TYPE_CREDIT,
                    'amount'       => $net,
                    'currency'     => 'USD',
                    'description'  => 'Payment via ' . $gateway->name,
                    'subject_type' => Payment::class,
                    'subject_id'   => $payment->id,
                    'balance_after' => $newBalance,
                    'meta'         => ['gateway' => $gateway->driver],
                    'created_at'   => $createdAt,
                ]);

                $user->update(['balance' => $newBalance]);
                $user->refresh();

                $seeded += $amount;
            }
        }

        // A few pending/failed ones for variety
        $pending = [
            ['amount' => 49.99, 'status' => Payment::STATUS_PENDING],
            ['amount' => 99.99, 'status' => Payment::STATUS_AWAITING_APPROVAL],
            ['amount' => 29.99, 'status' => Payment::STATUS_FAILED],
            ['amount' => 149.99, 'status' => Payment::STATUS_AWAITING_APPROVAL],
        ];

        foreach ($pending as $row) {
            Payment::create([
                'user_id'    => $users->random()->id,
                'gateway_id' => $stripeGateway->id,
                'amount'     => $row['amount'],
                'currency'   => 'USD',
                'fee'        => $stripeGateway->calculateFee($row['amount']),
                'status'     => $row['status'],
                'gateway_ref' => 'ref_' . uniqid(),
                'meta'       => ['demo' => true],
            ]);
        }
    }
}

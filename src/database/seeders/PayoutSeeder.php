<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\Payout;
use App\Domain\Payment\Models\PayoutMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class PayoutSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(15)->get();
        $methods = PayoutMethod::all();

        if ($users->isEmpty() || $methods->isEmpty()) {
            return;
        }

        $payoutData = [
            ['user_id' => 1, 'method_id' => 1, 'amount' => 100.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 1, 'method_id' => 2, 'amount' => 50.00, 'status' => Payout::STATUS_PENDING],
            ['user_id' => 2, 'method_id' => 1, 'amount' => 75.50, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 3, 'method_id' => 3, 'amount' => 150.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 4, 'method_id' => 4, 'amount' => 25.00, 'status' => Payout::STATUS_PENDING],
            ['user_id' => 5, 'method_id' => 5, 'amount' => 0.0025, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 6, 'method_id' => 2, 'amount' => 200.00, 'status' => Payout::STATUS_REJECTED],
            ['user_id' => 7, 'method_id' => 1, 'amount' => 500.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 8, 'method_id' => 4, 'amount' => 30.00, 'status' => Payout::STATUS_PENDING],
            ['user_id' => 9, 'method_id' => 3, 'amount' => 275.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 10, 'method_id' => 6, 'amount' => 0.015, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 11, 'method_id' => 2, 'amount' => 150.00, 'status' => Payout::STATUS_PENDING],
            ['user_id' => 12, 'method_id' => 1, 'amount' => 400.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 13, 'method_id' => 4, 'amount' => 60.00, 'status' => Payout::STATUS_CANCELLED],
            ['user_id' => 14, 'method_id' => 3, 'amount' => 350.00, 'status' => Payout::STATUS_APPROVED],
            ['user_id' => 15, 'method_id' => 2, 'amount' => 125.50, 'status' => Payout::STATUS_PENDING],
        ];

        foreach ($payoutData as $data) {
            $method = $methods->find($data['method_id']);
            if (!$method) {
                continue;
            }

            $payout = Payout::create([
                'user_id' => $data['user_id'],
                'payout_method_id' => $data['method_id'],
                'amount' => $data['amount'],
                'fee' => $method->calculateFee($data['amount']),
                'status' => $data['status'],
                'payout_details' => [
                    'demo' => true,
                    'method_name' => $method->getLocaleName('en'),
                ],
            ]);

            // Set reviewed_at for approved/rejected payouts
            if ($data['status'] === Payout::STATUS_APPROVED) {
                $payout->update([
                    'reviewed_at' => now()->subDays(rand(1, 30)),
                    'reviewed_by' => 1,
                ]);
            }

            // Set rejection reason for rejected payouts
            if ($data['status'] === Payout::STATUS_REJECTED) {
                $payout->update([
                    'reviewed_at' => now()->subDays(rand(1, 30)),
                    'reviewed_by' => 1,
                    'rejection_reason' => 'Insufficient balance on account',
                ]);
            }
        }
    }
}

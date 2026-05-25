<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\KycSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Each user is named to reflect its state combination for easy admin panel testing.
        // Password for all users: password
        $users = [

            // ── Active · Email verified · KYC approved · Phone verified ──────────
            [
                'username'            => 'alice.active.kyc',
                'name'                => 'Alice Johnson',
                'email'               => 'alice@example.com',
                'password'            => Hash::make('password'),
                'balance'             => 850.00,
                'country_code'        => 'US',
                'status'              => 'active',
                'email_verified_at'   => now(),
                'mobile_country_code' => '+1',
                'mobile'              => '5550100001',
                'mobile_verified_at'  => now(),
                'kyc_status'          => 'approved',
            ],

            // ── Active · Email verified · KYC approved · No phone ─────────────
            [
                'username'          => 'bob.active.kyc.nophone',
                'name'              => 'Bob Smith',
                'email'             => 'bob@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 320.50,
                'country_code'      => 'GB',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'approved',
            ],

            // ── Active · Email verified · KYC pending ────────────────────────
            [
                'username'          => 'carol.active.kyc.pending',
                'name'              => 'Carol Williams',
                'email'             => 'carol@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 120.00,
                'country_code'      => 'CA',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'pending',
            ],

            // ── Active · Email verified · KYC rejected ───────────────────────
            [
                'username'          => 'david.active.kyc.rejected',
                'name'              => 'David Brown',
                'email'             => 'david@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 0.00,
                'country_code'      => 'US',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'rejected',
            ],

            // ── Active · Email verified · KYC not submitted ──────────────────
            [
                'username'          => 'emma.active.nokyc',
                'name'              => 'Emma Davis',
                'email'             => 'emma@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 75.25,
                'country_code'      => 'AU',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'none',
            ],

            // ── Active · Email NOT verified ──────────────────────────────────
            [
                'username'     => 'frank.unverified.email',
                'name'         => 'Frank Miller',
                'email'        => 'frank@example.com',
                'password'     => Hash::make('password'),
                'balance'      => 0.00,
                'country_code' => 'DE',
                'status'       => 'active',
                'kyc_status'   => 'none',
            ],

            // ── Active · Email verified · Phone NOT verified ──────────────────
            [
                'username'            => 'grace.unverified.phone',
                'name'                => 'Grace Lee',
                'email'               => 'grace@example.com',
                'password'            => Hash::make('password'),
                'balance'             => 200.00,
                'country_code'        => 'SG',
                'status'              => 'active',
                'email_verified_at'   => now(),
                'mobile_country_code' => '+65',
                'mobile'              => '91234567',
                'kyc_status'          => 'none',
            ],

            // ── Inactive (banned) ─────────────────────────────────────────────
            [
                'username'          => 'henry.banned',
                'name'              => 'Henry Wilson',
                'email'             => 'henry@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 45.50,
                'country_code'      => 'IN',
                'status'            => 'banned',
                'email_verified_at' => now(),
                'kyc_status'        => 'none',
            ],

            // ── Inactive (suspended) ──────────────────────────────────────────
            [
                'username'          => 'iris.inactive',
                'name'              => 'Iris Martinez',
                'email'             => 'iris@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 600.25,
                'country_code'      => 'MX',
                'status'            => 'inactive',
                'email_verified_at' => now(),
                'kyc_status'        => 'none',
            ],

            // ── Active · High balance · KYC approved ─────────────────────────
            [
                'username'          => 'jack.highbalance',
                'name'              => 'Jack Anderson',
                'email'             => 'jack@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 9850.00,
                'country_code'      => 'NZ',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'approved',
            ],

            // ── Active · Zero balance · KYC approved ─────────────────────────
            [
                'username'          => 'kate.zerobalance',
                'name'              => 'Kate Thompson',
                'email'             => 'kate@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 0.00,
                'country_code'      => 'FR',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'approved',
            ],

            // ── Active · Email verified · KYC approved · Phone verified ──────
            [
                'username'            => 'leo.full.verified',
                'name'                => 'Leo Garcia',
                'email'               => 'leo@example.com',
                'password'            => Hash::make('password'),
                'balance'             => 430.75,
                'country_code'        => 'ES',
                'status'              => 'active',
                'email_verified_at'   => now(),
                'mobile_country_code' => '+34',
                'mobile'              => '612345678',
                'mobile_verified_at'  => now(),
                'kyc_status'          => 'approved',
            ],

            // ── Active · New signup · Nothing verified ────────────────────────
            [
                'username'     => 'maria.fresh.signup',
                'name'         => 'Maria Rodriguez',
                'email'        => 'maria@example.com',
                'password'     => Hash::make('password'),
                'balance'      => 0.00,
                'country_code' => 'BR',
                'status'       => 'active',
                'kyc_status'   => 'none',
            ],

            // ── Active · Email verified · KYC approved · Large balance ────────
            [
                'username'          => 'nathan.active.kyc',
                'name'              => 'Nathan Clark',
                'email'             => 'nathan@example.com',
                'password'          => Hash::make('password'),
                'balance'           => 2400.00,
                'country_code'      => 'IE',
                'status'            => 'active',
                'email_verified_at' => now(),
                'kyc_status'        => 'approved',
            ],

            // ── Active · Email verified · KYC pending · Has phone ────────────
            [
                'username'            => 'olivia.kyc.pending.phone',
                'name'                => 'Olivia White',
                'email'               => 'olivia@example.com',
                'password'            => Hash::make('password'),
                'balance'             => 150.00,
                'country_code'        => 'NL',
                'status'              => 'active',
                'email_verified_at'   => now(),
                'mobile_country_code' => '+31',
                'mobile'              => '612345678',
                'mobile_verified_at'  => now(),
                'kyc_status'          => 'pending',
            ],

            // ── Demo test user · Pro plan · Full access ───────────────────────
            [
                'username'            => 'testuser',
                'name'                => 'Test User',
                'email'               => 'testuser@example.com',
                'password'            => Hash::make('password'),
                'balance'             => 480.00,
                'country_code'        => 'US',
                'status'              => 'active',
                'email_verified_at'   => now(),
                'mobile_country_code' => '+1',
                'mobile'              => '5559870001',
                'mobile_verified_at'  => now(),
                'kyc_status'          => 'approved',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);

            // Seed KYC submissions for users with a kyc_status set
            if (! empty($data['kyc_status']) && $data['kyc_status'] !== '' && $user->wasRecentlyCreated) {
                $this->seedKycSubmission($user, $data['kyc_status']);
            }
        }
    }

    private function seedKycSubmission(User $user, string $status): void
    {
        $kycData = [
            'full_name'    => $user->name,
            'id_type'      => 'passport',
            'id_number'    => 'DEMO' . strtoupper(substr(md5($user->email), 0, 8)),
            'country_code' => $user->country_code ?? 'US',
        ];

        $rejectionReason = $status === KycSubmission::STATUS_REJECTED
            ? 'Document image was blurry. Please resubmit a clear photo.'
            : null;

        KycSubmission::create([
            'user_id'          => $user->id,
            'data'             => $kycData,
            'documents'        => [],
            'status'           => $status,
            'rejection_reason' => $rejectionReason,
            'reviewed_at'      => in_array($status, ['approved', 'rejected']) ? now() : null,
        ]);
    }
}

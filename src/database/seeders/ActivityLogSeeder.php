<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@example.com')->first();
        if (! $admin) {
            return;
        }

        $users = User::all()->keyBy('email');

        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

        $entries = [

            // ── User created on signup ────────────────────────────────────────
            [
                'action' => 'created',
                'subject' => $users['alice@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '192.168.1.10',
                'created_at' => now()->subDays(14),
            ],
            [
                'action' => 'created',
                'subject' => $users['bob@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '192.168.1.11',
                'created_at' => now()->subDays(13),
            ],
            [
                'action' => 'created',
                'subject' => $users['carol@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '10.0.0.5',
                'created_at' => now()->subDays(12),
            ],

            // ── Admin updated user status ─────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['henry@example.com'] ?? null,
                'properties' => [
                    'old' => ['status' => 'active'],
                    'attributes' => ['status' => 'banned'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(11),
            ],
            [
                'action' => 'updated',
                'subject' => $users['iris@example.com'] ?? null,
                'properties' => [
                    'old' => ['status' => 'active'],
                    'attributes' => ['status' => 'inactive'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(10),
            ],

            // ── Admin approved KYC ────────────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['alice@example.com'] ?? null,
                'properties' => [
                    'old' => ['kyc_status' => 'pending'],
                    'attributes' => ['kyc_status' => 'approved'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(10)->addHours(2),
            ],
            [
                'action' => 'updated',
                'subject' => $users['david@example.com'] ?? null,
                'properties' => [
                    'old' => ['kyc_status' => 'pending'],
                    'attributes' => ['kyc_status' => 'rejected'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(9),
            ],

            // ── Admin updated user email ──────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['frank@example.com'] ?? null,
                'properties' => [
                    'old' => ['email' => 'frank_old@example.com'],
                    'attributes' => ['email' => 'frank@example.com'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(8),
            ],

            // ── Admin reset user password ─────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['grace@example.com'] ?? null,
                'properties' => [
                    'old' => ['password' => '***'],
                    'attributes' => ['password' => '***'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(7),
            ],

            // ── User profile updates (self-service, causer = user) ────────────
            [
                'action' => 'updated',
                'subject' => $users['bob@example.com'] ?? null,
                'causer' => $users['bob@example.com'] ?? null,
                'properties' => [
                    'old' => ['name' => 'Robert Smith'],
                    'attributes' => ['name' => 'Bob Smith'],
                ],
                'ip_address' => '198.51.100.7',
                'created_at' => now()->subDays(6),
            ],

            // ── Admin adjusted balance ────────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['alice@example.com'] ?? null,
                'properties' => [
                    'old' => ['balance' => '500.00'],
                    'attributes' => ['balance' => '850.00'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(5),
            ],

            // ── New users created (recent) ────────────────────────────────────
            [
                'action' => 'created',
                'subject' => $users['emma@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '10.0.1.22',
                'created_at' => now()->subDays(4),
            ],
            [
                'action' => 'created',
                'subject' => $users['jack@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '10.0.1.23',
                'created_at' => now()->subDays(4)->addHours(3),
            ],

            // ── Admin verified email manually ─────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['frank@example.com'] ?? null,
                'properties' => [
                    'old' => ['email_verified_at' => null],
                    'attributes' => ['email_verified_at' => now()->subDays(3)->toDateTimeString()],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(3),
            ],

            // ── Admin assigned plan ───────────────────────────────────────────
            [
                'action' => 'created',
                'subject_type' => 'App\Domain\Subscription\Models\UserSubscription',
                'subject_id' => 1,
                'properties' => [],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(2),
            ],
            [
                'action' => 'created',
                'subject_type' => 'App\Domain\Subscription\Models\UserSubscription',
                'subject_id' => 2,
                'properties' => [],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDays(2)->addHours(1),
            ],

            // ── Admin deleted a user ──────────────────────────────────────────
            [
                'action' => 'deleted',
                'subject_type' => 'App\Models\User',
                'subject_id' => 999,
                'properties' => [],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subDay(),
            ],

            // ── Recent: ban lifted ────────────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['henry@example.com'] ?? null,
                'properties' => [
                    'old' => ['status' => 'banned'],
                    'attributes' => ['status' => 'active'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subHours(6),
            ],

            // ── Recent: KYC approved ──────────────────────────────────────────
            [
                'action' => 'updated',
                'subject' => $users['carol@example.com'] ?? null,
                'properties' => [
                    'old' => ['kyc_status' => 'pending'],
                    'attributes' => ['kyc_status' => 'approved'],
                ],
                'ip_address' => '203.0.113.42',
                'created_at' => now()->subHours(2),
            ],

            // ── Recent: new signup ────────────────────────────────────────────
            [
                'action' => 'created',
                'subject' => $users['maria@example.com'] ?? null,
                'properties' => [],
                'ip_address' => '172.16.0.8',
                'created_at' => now()->subMinutes(45),
            ],
        ];

        foreach ($entries as $entry) {
            $subject = $entry['subject'] ?? null;
            $causer = $entry['causer'] ?? $admin;

            ActivityLog::create([
                'causer_type' => $causer ? get_class($causer) : null,
                'causer_id' => $causer?->getKey(),
                'subject_type' => $subject ? get_class($subject) : ($entry['subject_type'] ?? null),
                'subject_id' => $subject ? $subject->getKey() : ($entry['subject_id'] ?? null),
                'action' => $entry['action'],
                'properties' => $entry['properties'],
                'ip_address' => $entry['ip_address'],
                'user_agent' => $ua,
                'created_at' => $entry['created_at'],
            ]);
        }
    }
}

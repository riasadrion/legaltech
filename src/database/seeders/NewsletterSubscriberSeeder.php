<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsletterSubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = [
            ['name' => 'Alice Johnson',   'email' => 'alice.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(30)],
            ['name' => 'Bob Smith',       'email' => 'bob.news@example.com',     'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(25)],
            ['name' => 'Carol Williams',  'email' => 'carol.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(20)],
            ['name' => 'David Brown',     'email' => 'david.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(18)],
            ['name' => 'Emma Davis',      'email' => 'emma.news@example.com',    'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(15)],
            ['name' => 'Frank Miller',    'email' => 'frank.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(12)],
            ['name' => 'Grace Lee',       'email' => 'grace.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(10)],
            ['name' => 'Henry Wilson',    'email' => 'henry.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(8)],
            ['name' => 'Iris Martinez',   'email' => 'iris.news@example.com',    'status' => Subscriber::STATUS_PENDING,      'confirmed_at' => null],
            ['name' => 'Jack Anderson',   'email' => 'jack.news@example.com',    'status' => Subscriber::STATUS_PENDING,      'confirmed_at' => null],
            ['name' => 'Kate Thompson',   'email' => 'kate.news@example.com',    'status' => Subscriber::STATUS_PENDING,      'confirmed_at' => null],
            ['name' => 'Leo Garcia',      'email' => 'leo.news@example.com',     'status' => Subscriber::STATUS_UNSUBSCRIBED, 'confirmed_at' => now()->subDays(40)],
            ['name' => 'Maria Rodriguez', 'email' => 'maria.news@example.com',   'status' => Subscriber::STATUS_UNSUBSCRIBED, 'confirmed_at' => now()->subDays(35)],
            ['name' => 'Nathan Clark',    'email' => 'nathan.news@example.com',  'status' => Subscriber::STATUS_UNSUBSCRIBED, 'confirmed_at' => now()->subDays(22)],
            ['name' => 'Olivia White',    'email' => 'olivia.news@example.com',  'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(5)],
            ['name' => 'Peter Harris',    'email' => 'peter.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDays(3)],
            ['name' => 'Quinn Martin',    'email' => 'quinn.news@example.com',   'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subDay()],
            ['name' => 'Rachel Green',    'email' => 'rachel.news@example.com',  'status' => Subscriber::STATUS_PENDING,      'confirmed_at' => null],
            ['name' => 'Samuel Taylor',   'email' => 'samuel.news@example.com',  'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subHours(6)],
            ['name' => 'Tina Robinson',   'email' => 'tina.news@example.com',    'status' => Subscriber::STATUS_CONFIRMED,    'confirmed_at' => now()->subHours(2)],
        ];

        foreach ($subscribers as $data) {
            Subscriber::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'         => $data['name'],
                    'status'       => $data['status'],
                    'confirmed_at' => $data['confirmed_at'],
                    'token'        => Subscriber::generateToken(),
                ]
            );
        }
    }
}

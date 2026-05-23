<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            LanguagesSeeder::class,
            CmsSeeder::class,
            BlogSeeder::class,
            NotificationSeeder::class,
            SuperAdminSeeder::class,
            UserSeeder::class,
            PaymentGatewaySeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
            PaymentSeeder::class,
            TicketSeeder::class,
            BlogPostSeeder::class,
            NewsletterSubscriberSeeder::class,
        ]);
    }
}

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
            SettingSeeder::class,
            SuperAdminSeeder::class,
            UserSeeder::class,
            PaymentGatewaySeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
            PaymentSeeder::class,
            ContractCategorySeeder::class,
            ContractSeeder::class,
            TicketSeeder::class,
            BlogPostSeeder::class,
            NewsletterSubscriberSeeder::class,
        ]);
    }
}

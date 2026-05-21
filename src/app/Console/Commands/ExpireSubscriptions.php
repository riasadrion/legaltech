<?php

namespace App\Console\Commands;

use App\Domain\Subscription\SubscriptionService;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscription:expire';

    protected $description = 'Mark overdue subscriptions as expired';

    public function handle(SubscriptionService $service): int
    {
        $service->expireOverdue();
        $this->info('Expired subscriptions processed.');

        return 0;
    }
}

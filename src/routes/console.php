<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('blog:publish-scheduled')->hourly();
Schedule::command('subscription:expire')->hourly();
Schedule::command('subscription:renew')->hourly();
Schedule::command('wallet:reconcile')->daily();
Schedule::command('queue:prune-failed', ['--hours' => 168])->daily();
Schedule::command('schedule:prune-batches', ['--hours' => 48])->daily();

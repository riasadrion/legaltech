<?php

namespace App\Notifications\Contracts;

use App\Models\NotificationTemplate;
use App\Models\User;

interface NotificationChannel
{
    public function name(): string;

    public function send(User $user, NotificationTemplate $template, array $vars, string $locale): bool;
}

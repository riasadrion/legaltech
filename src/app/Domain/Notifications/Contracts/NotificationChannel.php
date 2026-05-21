<?php

namespace App\Domain\Notifications\Contracts;

use App\Domain\Notifications\Models\NotificationTemplate;
use App\Models\User;

interface NotificationChannel
{
    public function name(): string;

    public function send(User $user, NotificationTemplate $template, array $vars, string $locale): bool;
}

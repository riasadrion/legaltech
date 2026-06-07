<?php

namespace App\Notifications\Channels;

use App\Models\NotificationTemplate;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\Contracts\NotificationChannel;

class InAppChannel implements NotificationChannel
{
    public function name(): string
    {
        return 'in_app';
    }

    public function send(User $user, NotificationTemplate $template, array $vars, string $locale): bool
    {
        $subject = $template->renderSubject($vars, $locale);
        $body = $template->renderEmailBody($vars, $locale);

        $notif = new UserNotification;
        $notif->user_id = $user->id;
        $notif->type = $template->key;
        $notif->title = $subject ?: $template->name;
        $notif->body = strip_tags($body);
        $notif->save();

        return true;
    }
}

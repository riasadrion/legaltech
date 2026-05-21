<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\InAppChannel;
use App\Domain\Notifications\Channels\SmsChannel;
use App\Domain\Notifications\Models\NotificationLog;
use App\Domain\Notifications\Models\NotificationPreference;
use App\Domain\Notifications\Models\NotificationSuppression;
use App\Domain\Notifications\Models\NotificationTemplate;
use App\Models\User;

class Notify
{
    public static function send(User $user, string $key, array $vars = []): void
    {
        $template = NotificationTemplate::where('key', $key)->where('is_active', true)->first();

        if ($template === null) {
            return;
        }

        $locale = $user->locale ?? app()->getLocale();

        $vars = array_merge([
            'user.name' => $user->name ?? $user->username ?? 'User',
            'user.email' => $user->email,
            'app.name' => config('app.name'),
            'app.url' => config('app.url'),
        ], $vars);

        foreach ($template->channels as $channel) {
            if (! NotificationPreference::isEnabled($user->id, $key, $channel)) {
                self::logSkipped($template, $user, $channel, $locale, 'preference');

                continue;
            }

            if ($channel === 'email' && NotificationSuppression::isSuppressed($user->email)) {
                self::logSkipped($template, $user, $channel, $locale, 'suppressed');

                continue;
            }

            self::sendChannel($channel, $user, $template, $vars, $locale);
        }
    }

    private static function sendChannel(string $channel, User $user, NotificationTemplate $template, array $vars, string $locale): void
    {
        $driver = match ($channel) {
            'email' => app(EmailChannel::class),
            'sms' => app(SmsChannel::class),
            'in_app' => app(InAppChannel::class),
            default => null,
        };

        if ($driver === null) {
            return;
        }

        try {
            $sent = $driver->send($user, $template, $vars, $locale);
            $status = $sent ? NotificationLog::STATUS_SENT : NotificationLog::STATUS_FAILED;
        } catch (\Throwable $e) {
            $status = NotificationLog::STATUS_FAILED;
            $error = $e->getMessage();
        }

        $log = new NotificationLog;
        $log->notification_key = $template->key;
        $log->channel = $channel;
        $log->notifiable_type = User::class;
        $log->notifiable_id = $user->id;
        $log->recipient = $channel === 'email' ? $user->email : ($user->mobile ?? null);
        $log->subject = $channel === 'email' ? $template->renderSubject($vars, $locale) : null;
        $log->body = $channel === 'email' ? $template->renderEmailBody($vars, $locale) : $template->renderSmsBody($vars, $locale);
        $log->status = $status ?? NotificationLog::STATUS_SENT;
        $log->error = $error ?? null;
        $log->locale = $locale;
        $log->save();
    }

    private static function logSkipped(NotificationTemplate $template, User $user, string $channel, string $locale, string $reason): void
    {
        $log = new NotificationLog;
        $log->notification_key = $template->key;
        $log->channel = $channel;
        $log->notifiable_type = User::class;
        $log->notifiable_id = $user->id;
        $log->recipient = $user->email;
        $log->status = NotificationLog::STATUS_SKIPPED;
        $log->error = $reason;
        $log->locale = $locale;
        $log->save();
    }
}

<?php

namespace App\Notifications\Channels;

use App\Notifications\Contracts\NotificationChannel;
use App\Models\NotificationTemplate;
use App\Facades\Settings;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements NotificationChannel
{
    public function name(): string
    {
        return 'email';
    }

    public function send(User $user, NotificationTemplate $template, array $vars, string $locale): bool
    {
        $subject = $template->renderSubject($vars, $locale);
        $htmlBody = $template->renderEmailBody($vars, $locale);
        $textBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));

        $fromEmail = Settings::get('mail.from_address', config('mail.from.address'));
        $fromName = Settings::get('mail.from_name', config('mail.from.name', config('app.name')));

        $unsubToken = base64_encode($user->email.'|'.hash_hmac('sha256', $user->email, config('app.key')));
        $unsubUrl = route('notifications.unsubscribe', ['token' => $unsubToken]);

        $layoutHtml = $this->wrapInLayout($htmlBody, $unsubUrl, $locale);

        Mail::send([], [], function (Message $message) use (
            $user, $subject, $layoutHtml, $textBody, $fromEmail, $fromName, $unsubUrl
        ) {
            $message->to($user->email, $user->name ?? $user->username)
                ->from($fromEmail, $fromName)
                ->subject($subject)
                ->html($layoutHtml)
                ->text($textBody."\n\nUnsubscribe: ".$unsubUrl)
                ->addTextHeader('List-Unsubscribe', '<'.$unsubUrl.'>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return true;
    }

    private function wrapInLayout(string $body, string $unsubUrl, string $locale): string
    {
        $layout = NotificationTemplate::where('key', 'email_layout')->first();

        $vars = [
            'app.name' => config('app.name'),
            'app.url' => config('app.url'),
            'content' => $body,
            'unsubscribe.url' => $unsubUrl,
        ];

        if ($layout !== null) {
            return $layout->renderEmailBody($vars, $locale);
        }

        return $body;
    }
}

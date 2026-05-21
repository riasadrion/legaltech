<?php

namespace App\Domain\Notifications\Channels;

use App\Domain\Notifications\Contracts\NotificationChannel;
use App\Domain\Notifications\Models\NotificationTemplate;
use App\Domain\Settings\Facades\Settings;
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

        Mail::send([], [], function (Message $message) use (
            $user, $subject, $htmlBody, $textBody, $fromEmail, $fromName, $unsubUrl
        ) {
            $message->to($user->email, $user->name ?? $user->username)
                ->from($fromEmail, $fromName)
                ->subject($subject)
                ->html($this->wrapInLayout($htmlBody, $unsubUrl))
                ->text($textBody."\n\nUnsubscribe: ".$unsubUrl)
                ->addTextHeader('List-Unsubscribe', '<'.$unsubUrl.'>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return true;
    }

    private function wrapInLayout(string $body, string $unsubUrl): string
    {
        $appName = htmlspecialchars(config('app.name'), ENT_QUOTES, 'UTF-8');
        $appUrl = config('app.url');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$appName}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
      <tr><td style="background:#1e40af;padding:24px 32px;">
        <a href="{$appUrl}" style="color:#ffffff;font-size:20px;font-weight:bold;text-decoration:none;">{$appName}</a>
      </td></tr>
      <tr><td style="padding:32px;color:#111827;font-size:15px;line-height:1.6;">
        {$body}
      </td></tr>
      <tr><td style="padding:24px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#6b7280;">
        <p style="margin:0 0 8px;">&copy; {$appName} &mdash; <a href="{$appUrl}" style="color:#1e40af;">{$appUrl}</a></p>
        <p style="margin:0;"><a href="{$unsubUrl}" style="color:#6b7280;">Unsubscribe</a></p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}

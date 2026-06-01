<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterBroadcast extends Mailable
{
    public function __construct(
        public string $subject,
        public string $body,
        public string $unsubscribeToken,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subject);
    }

    public function content(): Content
    {
        $appName = config('app.name');
        $unsubscribeUrl = route('newsletter.unsubscribe', $this->unsubscribeToken);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#374151;max-width:560px;margin:0 auto;padding:32px 16px;">
    {$this->body}
    <hr style="margin:32px 0;border:none;border-top:1px solid #e5e7eb;">
    <p style="font-size:12px;color:#9ca3af;">
        You're receiving this because you subscribed to {$appName}.<br>
        <a href="{$unsubscribeUrl}" style="color:#9ca3af;">Unsubscribe</a>
    </p>
</body>
</html>
HTML;

        return new Content(htmlString: $html);
    }
}

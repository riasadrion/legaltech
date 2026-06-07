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
        return new Content(
            view: 'emails.newsletter-broadcast',
            with: [
                'appName' => config('app.name'),
                'body' => $this->body,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $this->unsubscribeToken),
            ],
        );
    }
}

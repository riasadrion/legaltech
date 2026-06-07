<?php

namespace App\Http\Controllers\Webhooks;

use App\Facades\Settings;
use App\Models\EmailEvent;
use App\Models\NotificationSuppression;
use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailgunWebhookController
{
    public function handle(Request $request): Response
    {
        if (! $this->verifySignature($request)) {
            abort(401, 'Invalid signature');
        }

        $eventData = $request->input('event-data', []);
        $event = $eventData['event'] ?? $request->input('event');
        $email = $eventData['recipient'] ?? $request->input('recipient');
        $messageId = $eventData['message']['headers']['message-id'] ?? null;

        if (! $email || ! $event) {
            return response('', 200);
        }

        $idempotencyKey = 'mailgun.'.($messageId ?? $email).'.'.$event;
        if (WebhookEvent::where('idempotency_key', $idempotencyKey)->exists()) {
            return response('', 200);
        }

        $webhookEvent = new WebhookEvent;
        $webhookEvent->provider = 'mailgun';
        $webhookEvent->idempotency_key = $idempotencyKey;
        $webhookEvent->payload = $request->all();
        $webhookEvent->save();

        $mapped = match ($event) {
            'delivered' => EmailEvent::EVENT_DELIVERED,
            'opened' => EmailEvent::EVENT_OPENED,
            'clicked' => EmailEvent::EVENT_CLICKED,
            'permanent_fail', 'failed' => EmailEvent::EVENT_BOUNCED,
            'complained' => EmailEvent::EVENT_COMPLAINED,
            default => null,
        };

        if ($mapped) {
            $emailEvent = new EmailEvent;
            $emailEvent->provider = 'mailgun';
            $emailEvent->message_id = $messageId;
            $emailEvent->email = strtolower($email);
            $emailEvent->event = $mapped;
            $emailEvent->payload = $eventData;
            $emailEvent->occurred_at = now();
            $emailEvent->save();

            if (in_array($mapped, [EmailEvent::EVENT_BOUNCED, EmailEvent::EVENT_COMPLAINED])) {
                $reason = $mapped === EmailEvent::EVENT_BOUNCED
                    ? NotificationSuppression::REASON_BOUNCE
                    : NotificationSuppression::REASON_COMPLAINT;
                NotificationSuppression::suppress($email, $reason, 'mailgun');

                $user = User::where('email', strtolower($email))->first();
                if ($user) {
                    $user->email_verified_at = null;
                    $user->save();
                }
            }
        }

        return response('', 200);
    }

    private function verifySignature(Request $request): bool
    {
        $signingKey = Settings::get('mail.mailgun.webhook_signing_key');
        if (! $signingKey) {
            return true;
        }

        $signature = $request->input('signature', []);
        $timestamp = $signature['timestamp'] ?? $request->input('timestamp');
        $token = $signature['token'] ?? $request->input('token');
        $sig = $signature['signature'] ?? $request->input('signature');

        if (! $timestamp || ! $token || ! $sig) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.$token, $signingKey);

        return hash_equals($expected, $sig);
    }
}

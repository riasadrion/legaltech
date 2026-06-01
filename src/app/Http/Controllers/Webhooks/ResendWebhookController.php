<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\EmailEvent;
use App\Models\NotificationSuppression;
use App\Facades\Settings;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResendWebhookController
{
    public function handle(Request $request): Response
    {
        $secret = Settings::get('mail.resend.webhook_secret');
        if ($secret) {
            $svixId = $request->header('svix-id');
            $svixTimestamp = $request->header('svix-timestamp');
            $svixSignature = $request->header('svix-signature');

            $toSign = $svixId.'.'.$svixTimestamp.'.'.$request->getContent();
            $expected = base64_encode(hash_hmac('sha256', $toSign, base64_decode(str_replace('whsec_', '', $secret)), true));

            $valid = collect(explode(' ', (string) $svixSignature))
                ->map(fn ($s) => substr($s, 3))
                ->contains(fn ($s) => hash_equals($expected, $s));

            if (! $valid) {
                abort(401, 'Invalid signature');
            }
        }

        $body = $request->json()->all();
        $eventType = $body['type'] ?? null;
        $data = $body['data'] ?? [];
        $email = $data['to'][0] ?? null;
        $messageId = $data['email_id'] ?? null;

        if (! $eventType || ! $email) {
            return response('', 200);
        }

        $idempotencyKey = 'resend.'.($messageId ?? $email).'.'.$eventType;
        if (WebhookEvent::where('idempotency_key', $idempotencyKey)->exists()) {
            return response('', 200);
        }

        $webhookEvent = new WebhookEvent;
        $webhookEvent->provider = 'resend';
        $webhookEvent->idempotency_key = $idempotencyKey;
        $webhookEvent->payload = $body;
        $webhookEvent->save();

        $mapped = match ($eventType) {
            'email.delivered' => EmailEvent::EVENT_DELIVERED,
            'email.opened' => EmailEvent::EVENT_OPENED,
            'email.clicked' => EmailEvent::EVENT_CLICKED,
            'email.bounced' => EmailEvent::EVENT_BOUNCED,
            'email.complained' => EmailEvent::EVENT_COMPLAINED,
            default => null,
        };

        if ($mapped) {
            $emailEvent = new EmailEvent;
            $emailEvent->provider = 'resend';
            $emailEvent->message_id = $messageId;
            $emailEvent->email = strtolower(is_array($email) ? $email[0] : $email);
            $emailEvent->event = $mapped;
            $emailEvent->payload = $data;
            $emailEvent->occurred_at = now();
            $emailEvent->save();

            if (in_array($mapped, [EmailEvent::EVENT_BOUNCED, EmailEvent::EVENT_COMPLAINED])) {
                $reason = $mapped === EmailEvent::EVENT_BOUNCED
                    ? NotificationSuppression::REASON_BOUNCE
                    : NotificationSuppression::REASON_COMPLAINT;
                NotificationSuppression::suppress(is_array($email) ? $email[0] : $email, $reason, 'resend');
            }
        }

        return response('', 200);
    }
}

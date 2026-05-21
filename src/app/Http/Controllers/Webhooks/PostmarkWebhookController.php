<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Notifications\Models\EmailEvent;
use App\Domain\Notifications\Models\NotificationSuppression;
use App\Domain\Settings\Facades\Settings;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostmarkWebhookController
{
    public function handle(Request $request): Response
    {
        $secret = Settings::get('mail.postmark.webhook_secret');
        if ($secret && $request->header('X-Postmark-Secret') !== $secret) {
            abort(401, 'Invalid secret');
        }

        $body = $request->json()->all();
        $eventType = $body['RecordType'] ?? null;
        $messageId = $body['MessageID'] ?? null;
        $email = $body['Recipient'] ?? $body['Email'] ?? null;

        if (! $eventType || ! $email) {
            return response('', 200);
        }

        $idempotencyKey = 'postmark.'.($messageId ?? $email).'.'.$eventType;
        if (WebhookEvent::where('idempotency_key', $idempotencyKey)->exists()) {
            return response('', 200);
        }

        $webhookEvent = new WebhookEvent;
        $webhookEvent->provider = 'postmark';
        $webhookEvent->idempotency_key = $idempotencyKey;
        $webhookEvent->payload = $body;
        $webhookEvent->save();

        $mapped = match ($eventType) {
            'Delivery' => EmailEvent::EVENT_DELIVERED,
            'Open' => EmailEvent::EVENT_OPENED,
            'Click' => EmailEvent::EVENT_CLICKED,
            'Bounce' => EmailEvent::EVENT_BOUNCED,
            'SpamComplaint' => EmailEvent::EVENT_COMPLAINED,
            default => null,
        };

        if ($mapped) {
            $emailEvent = new EmailEvent;
            $emailEvent->provider = 'postmark';
            $emailEvent->message_id = $messageId;
            $emailEvent->email = strtolower($email);
            $emailEvent->event = $mapped;
            $emailEvent->payload = $body;
            $emailEvent->occurred_at = now();
            $emailEvent->save();

            if (in_array($mapped, [EmailEvent::EVENT_BOUNCED, EmailEvent::EVENT_COMPLAINED])) {
                $reason = $mapped === EmailEvent::EVENT_BOUNCED
                    ? NotificationSuppression::REASON_BOUNCE
                    : NotificationSuppression::REASON_COMPLAINT;

                $isHardBounce = ($eventType === 'Bounce') && in_array($body['Type'] ?? '', ['HardBounce', 'BadEmailAddress']);
                if ($isHardBounce || $mapped === EmailEvent::EVENT_COMPLAINED) {
                    NotificationSuppression::suppress($email, $reason, 'postmark');
                }
            }
        }

        return response('', 200);
    }
}

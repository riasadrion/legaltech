<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\EmailEvent;
use App\Models\NotificationSuppression;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SesWebhookController
{
    public function handle(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (! $body) {
            return response('', 200);
        }

        $type = $body['Type'] ?? null;

        if ($type === 'SubscriptionConfirmation') {
            @file_get_contents($body['SubscribeURL']);

            return response('', 200);
        }

        if ($type !== 'Notification') {
            return response('', 200);
        }

        $message = json_decode($body['Message'] ?? '{}', true);
        if (! $message) {
            return response('', 200);
        }

        $notifType = $message['notificationType'] ?? $message['eventType'] ?? null;
        $messageId = $message['mail']['messageId'] ?? null;

        $idempotencyKey = 'ses.'.($messageId ?? uniqid());
        if (WebhookEvent::where('idempotency_key', $idempotencyKey)->exists()) {
            return response('', 200);
        }

        $webhookEvent = new WebhookEvent;
        $webhookEvent->provider = 'ses';
        $webhookEvent->idempotency_key = $idempotencyKey;
        $webhookEvent->payload = $message;
        $webhookEvent->save();

        $mapped = match (strtolower((string) $notifType)) {
            'delivery' => EmailEvent::EVENT_DELIVERED,
            'open' => EmailEvent::EVENT_OPENED,
            'click' => EmailEvent::EVENT_CLICKED,
            'bounce' => EmailEvent::EVENT_BOUNCED,
            'complaint' => EmailEvent::EVENT_COMPLAINED,
            default => null,
        };

        if (! $mapped) {
            return response('', 200);
        }

        $emails = match ($mapped) {
            EmailEvent::EVENT_BOUNCED => array_column($message['bounce']['bouncedRecipients'] ?? [], 'emailAddress'),
            EmailEvent::EVENT_COMPLAINED => array_column($message['complaint']['complainedRecipients'] ?? [], 'emailAddress'),
            default => $message['mail']['destination'] ?? [],
        };

        foreach ($emails as $email) {
            $emailEvent = new EmailEvent;
            $emailEvent->provider = 'ses';
            $emailEvent->message_id = $messageId;
            $emailEvent->email = strtolower($email);
            $emailEvent->event = $mapped;
            $emailEvent->payload = $message;
            $emailEvent->occurred_at = now();
            $emailEvent->save();

            if (in_array($mapped, [EmailEvent::EVENT_BOUNCED, EmailEvent::EVENT_COMPLAINED])) {
                $reason = $mapped === EmailEvent::EVENT_BOUNCED
                    ? NotificationSuppression::REASON_BOUNCE
                    : NotificationSuppression::REASON_COMPLAINT;
                NotificationSuppression::suppress($email, $reason, 'ses');
            }
        }

        return response('', 200);
    }
}

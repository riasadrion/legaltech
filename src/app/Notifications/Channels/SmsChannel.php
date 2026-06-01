<?php

namespace App\Notifications\Channels;

use App\Notifications\Contracts\NotificationChannel;
use App\Models\NotificationTemplate;
use App\Facades\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SmsChannel implements NotificationChannel
{
    public function name(): string
    {
        return 'sms';
    }

    public function send(User $user, NotificationTemplate $template, array $vars, string $locale): bool
    {
        if (empty($user->mobile)) {
            return false;
        }

        $body = $template->renderSmsBody($vars, $locale);
        $driver = Settings::get('sms.driver', 'log');

        return match ($driver) {
            'twilio' => $this->sendViaTwilio($user->mobile, $body),
            'vonage' => $this->sendViaVonage($user->mobile, $body),
            'messagebird' => $this->sendViaMessageBird($user->mobile, $body),
            'textmagic' => $this->sendViaTextmagic($user->mobile, $body),
            default => $this->logSms($user->mobile, $body),
        };
    }

    private function sendViaTwilio(string $to, string $body): bool
    {
        $sid = Settings::get('sms.twilio.sid');
        $token = Settings::get('sms.twilio.token');
        $from = Settings::get('sms.twilio.from');

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'To' => $to,
                'From' => $from,
                'Body' => $body,
            ]);

        return $response->successful() && $response->json('sid') !== null;
    }

    private function sendViaVonage(string $to, string $body): bool
    {
        $key = Settings::get('sms.vonage.api_key');
        $secret = Settings::get('sms.vonage.api_secret');
        $from = Settings::get('sms.vonage.from', 'Starter');

        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => $key,
            'api_secret' => $secret,
            'to' => $to,
            'from' => $from,
            'text' => $body,
        ]);

        return ($response->json('messages.0.status') ?? '1') === '0';
    }

    private function sendViaMessageBird(string $to, string $body): bool
    {
        $apiKey = Settings::get('sms.messagebird.api_key');
        $from = Settings::get('sms.messagebird.from', 'Starter');

        $response = Http::withToken($apiKey, 'AccessKey')
            ->post('https://rest.messagebird.com/messages', [
                'recipients' => $to,
                'originator' => $from,
                'body' => $body,
            ]);

        return $response->successful() && $response->json('id') !== null;
    }

    private function sendViaTextmagic(string $to, string $body): bool
    {
        $username = Settings::get('sms.textmagic.username');
        $apiKey = Settings::get('sms.textmagic.api_key');

        $response = Http::withBasicAuth($username, $apiKey)
            ->post('https://rest.textmagic.com/api/v2/messages', [
                'phones' => $to,
                'text' => $body,
            ]);

        return $response->successful() && $response->json('id') !== null;
    }

    private function logSms(string $to, string $body): bool
    {
        logger()->info("SMS [log driver] to={$to} body={$body}");

        return true;
    }
}

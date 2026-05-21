<?php

namespace App\Domain\Auth\PhoneVerification;

use App\Domain\Settings\Facades\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneVerificationService
{
    private const CODE_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;

    public function isEnabled(): bool
    {
        return Settings::string('controls.sms_verification') === '1';
    }

    public function sendCode(User $user, string $phone): array
    {
        // Cooldown check — block too-rapid resends
        $recent = PhoneVerificationCode::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($recent && $recent->created_at->diffInSeconds(now()) < self::RESEND_COOLDOWN_SECONDS) {
            $wait = self::RESEND_COOLDOWN_SECONDS - $recent->created_at->diffInSeconds(now());

            return ['sent' => false, 'reason' => "Please wait {$wait}s before requesting another code."];
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PhoneVerificationCode::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
        ]);

        $sent = $this->dispatchSms($phone, "Your verification code is: {$code}");

        return ['sent' => $sent, 'reason' => $sent ? null : 'Failed to send SMS. Check your SMS settings.'];
    }

    public function verify(User $user, string $code): bool
    {
        $record = PhoneVerificationCode::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $record || $record->isExpired() || $record->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $record->increment('attempts');

        if (! Hash::check($code, $record->code_hash)) {
            return false;
        }

        $record->update(['verified_at' => now()]);

        $user->forceFill([
            'mobile' => $record->phone,
            'mobile_verified_at' => now(),
        ])->save();

        return true;
    }

    private function dispatchSms(string $to, string $body): bool
    {
        $driver = Settings::string('sms.driver', 'log');

        try {
            return match ($driver) {
                'twilio' => $this->sendViaTwilio($to, $body),
                'vonage' => $this->sendViaVonage($to, $body),
                'messagebird' => $this->sendViaMessageBird($to, $body),
                'textmagic' => $this->sendViaTextmagic($to, $body),
                default => $this->logSms($to, $body),
            };
        } catch (\Throwable $e) {
            Log::warning('SMS send failed: '.$e->getMessage());

            return false;
        }
    }

    private function sendViaTwilio(string $to, string $body): bool
    {
        $sid = Settings::string('sms.twilio.sid');
        $token = Settings::string('sms.twilio.token');
        $from = Settings::string('sms.twilio.from');

        $response = Http::withBasicAuth($sid, $token)->asForm()->post(
            "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json",
            ['To' => $to, 'From' => $from, 'Body' => $body],
        );

        return $response->successful();
    }

    private function sendViaVonage(string $to, string $body): bool
    {
        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => Settings::string('sms.vonage.api_key'),
            'api_secret' => Settings::string('sms.vonage.api_secret'),
            'to' => $to,
            'from' => Settings::string('sms.vonage.from', 'Starter'),
            'text' => $body,
        ]);

        return ($response->json('messages.0.status') ?? '1') === '0';
    }

    private function sendViaMessageBird(string $to, string $body): bool
    {
        $response = Http::withToken(Settings::string('sms.messagebird.api_key'), 'AccessKey')->post(
            'https://rest.messagebird.com/messages',
            ['recipients' => $to, 'originator' => Settings::string('sms.messagebird.from', 'Starter'), 'body' => $body],
        );

        return $response->successful();
    }

    private function sendViaTextmagic(string $to, string $body): bool
    {
        $response = Http::withBasicAuth(
            Settings::string('sms.textmagic.username'),
            Settings::string('sms.textmagic.api_key'),
        )->post('https://rest.textmagic.com/api/v2/messages', ['phones' => $to, 'text' => $body]);

        return $response->successful();
    }

    private function logSms(string $to, string $body): bool
    {
        Log::info("SMS [log driver] to={$to} body={$body}");

        return true;
    }
}

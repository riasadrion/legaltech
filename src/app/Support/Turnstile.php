<?php

namespace App\Support;

use App\Domain\Settings\Facades\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function enabled(): bool
    {
        return Settings::string('integrations.turnstile.enabled') === '1'
            && Settings::string('integrations.turnstile.site_key') !== ''
            && Settings::string('integrations.turnstile.secret_key') !== '';
    }

    public static function siteKey(): string
    {
        return Settings::string('integrations.turnstile.site_key');
    }

    public static function verify(?string $token, ?string $ip = null): bool
    {
        if (! self::enabled()) {
            return true;
        }

        if (! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(self::VERIFY_URL, [
                'secret' => Settings::string('integrations.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            Log::warning('Turnstile verify failed: '.$e->getMessage());

            return false;
        }
    }
}

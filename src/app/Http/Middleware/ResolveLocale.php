<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);

        try {
            $language = Language::findByCode($locale) ?? Language::default();
        } catch (\Throwable) {
            $language = null;
        }

        if ($language) {
            $request->attributes->set('language', $language);
            view()->share('currentLanguage', $language);
            view()->share('htmlDir', $language->direction);
        } else {
            view()->share('htmlDir', 'ltr');
        }

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Session (set by LocaleController when user picks a language)
        $session = $request->session()->get('locale');
        if ($session && $this->isValidLocale($session)) {
            return $session;
        }

        // 2. Authenticated user's saved locale
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && isset($user->locale) && is_string($user->locale) && $this->isValidLocale($user->locale)) {
            return $user->locale;
        }

        // 3. Cookie (long-lived, set by LocaleController)
        $cookie = $request->cookie('locale');
        if ($cookie && $this->isValidLocale($cookie)) {
            return $cookie;
        }

        // 4. Accept-Language header
        $accepted = $this->parseAcceptLanguage($request->header('Accept-Language', ''));
        foreach ($accepted as $lang) {
            if ($this->isValidLocale($lang)) {
                return $lang;
            }
        }

        // 5. Default language from DB, then config fallback
        try {
            $default = Language::default();
        } catch (\Throwable) {
            $default = null;
        }

        return $default?->code ?? config('app.locale', 'en');
    }

    private function isValidLocale(string $code): bool
    {
        static $cache = [];

        if (isset($cache[$code])) {
            return $cache[$code];
        }

        if (! preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $code)) {
            return $cache[$code] = false;
        }

        try {
            return $cache[$code] = Language::where('code', $code)->where('is_active', true)->exists();
        } catch (\Throwable) {
            return $cache[$code] = false;
        }
    }

    private function parseAcceptLanguage(string $header): array
    {
        if (empty($header)) {
            return [];
        }

        $parts = explode(',', $header);
        $locales = [];

        foreach ($parts as $part) {
            [$lang] = explode(';', trim($part));
            // Normalise 'en-US' → 'en' for lookup; keep original too
            $code = strtolower(trim(explode('-', $lang)[0]));
            if ($code) {
                $locales[] = $code;
            }
        }

        return $locales;
    }
}

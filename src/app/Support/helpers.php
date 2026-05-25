<?php

use App\Domain\Cms\Sanitizer\HtmlPurifier;
use App\Domain\Settings\Facades\Settings;

if (! function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        static $cache = [];

        $cacheKey = $key . ':' . $default;

        if (! array_key_exists($cacheKey, $cache)) {
            $cache[$cacheKey] = Settings::string($key, $default);
        }

        return $cache[$cacheKey];
    }
}

if (! function_exists('purify')) {
    function purify(?string $html, ?array $allowedTags = null): string
    {
        return app(HtmlPurifier::class)->clean($html ?? '', $allowedTags);
    }
}

if (! function_exists('safe_json')) {
    function safe_json(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
        );
    }
}

if (! function_exists('upload_url')) {
    /**
     * Build a public URL from a relative upload path (e.g. "sections/file.png").
     * Returns the value unchanged if it is already an absolute URL.
     */
    function upload_url(?string $path): string
    {
        if (! $path) {
            return '';
        }
        $base = rtrim(request()->getSchemeAndHttpHost(), '') . rtrim(request()->getBasePath(), '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Strip stored host and rebuild with current host+basepath
            $parsed = parse_url($path);
            $filePath = $parsed['path'] ?? $path;
            // Remove any known assets/uploads prefix to get the relative path
            $filePath = preg_replace('#^.*?/assets/uploads/#', '', $filePath);
            return $base . '/assets/uploads/' . ltrim($filePath, '/');
        }
        return $base . '/assets/uploads/' . ltrim($path, '/');
    }
}

if (! function_exists('isCryptoCurrency')) {
    /**
     * Check if a currency code is a cryptocurrency.
     */
    function isCryptoCurrency(string $currency): bool
    {
        $cryptoCurrencies = ['BTC', 'ETH', 'USDT', 'USDC', 'BNB', 'XRP', 'ADA', 'SOL', 'DOT', 'DOGE'];
        return in_array(strtoupper($currency), $cryptoCurrencies);
    }
}

if (! function_exists('formatDate')) {
    function formatDate(mixed $date, bool $withTime = true): string
    {
        if (! $date) {
            return '—';
        }
        $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        return $withTime ? $carbon->format('d M Y - H:i') : $carbon->format('d M Y');
    }
}

if (! function_exists('formatAmount')) {
    /**
     * Format amount with proper decimals.
     * Fiat: 2 decimals, Crypto: 8 decimals
     */
    function formatAmount(float $amount, ?string $currency = null, bool $withSymbol = true): string
    {
        $currency = $currency ?? Settings::string('site.currency', 'USD');
        $decimals = isCryptoCurrency($currency) ? 8 : 2;
        $formatted = number_format($amount, $decimals);
        
        if (! $withSymbol) {
            return $formatted;
        }
        
        $symbol = Settings::string('site.currency_symbol', '$');
        return $symbol . $formatted;
    }
}

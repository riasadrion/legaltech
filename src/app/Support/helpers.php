<?php

use App\Facades\Settings;
use App\Services\HtmlPurifier;
use Carbon\Carbon;

if (! function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        static $cache = [];

        $cacheKey = $key.':'.$default;

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
    function upload_url(?string $path): string
    {
        if (! $path) {
            return '';
        }
        $base = rtrim(request()->getSchemeAndHttpHost(), '').rtrim(request()->getBasePath(), '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path);
            $filePath = $parsed['path'] ?? $path;
            $filePath = preg_replace('#^.*?/assets/uploads/#', '', $filePath);

            return $base.'/assets/uploads/'.ltrim($filePath, '/');
        }

        return $base.'/assets/uploads/'.ltrim($path, '/');
    }
}

if (! function_exists('link_url')) {
    function link_url(?string $raw, string $default = '#'): string
    {
        $raw = $raw ?: $default;

        if ($raw === '#' || str_starts_with($raw, '#') || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, '//') || str_starts_with($raw, 'mailto:') || str_starts_with($raw, 'tel:')) {
            return $raw;
        }

        $base = rtrim(request()->getSchemeAndHttpHost(), '').rtrim(request()->getBasePath(), '/');

        return $base.'/'.ltrim($raw, '/');
    }
}

if (! function_exists('isCryptoCurrency')) {
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
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

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

        return $symbol.$formatted;
    }
}

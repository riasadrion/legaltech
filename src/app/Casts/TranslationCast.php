<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TranslationCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;

        if (! is_array($decoded)) {
            return (string) ($value ?? '');
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return (string) ($decoded[$locale] ?? $decoded[$fallback] ?? reset($decoded) ?? '');
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        $existing = isset($attributes[$key])
            ? (json_decode($attributes[$key], true) ?? [])
            : [];

        if (is_array($value)) {
            $merged = array_merge($existing, $value);
        } else {
            $locale = app()->getLocale();
            $existing[$locale] = $value;
            $merged = $existing;
        }

        return json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}

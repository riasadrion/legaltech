<?php

namespace App\Domain\I18n\Concerns;

trait HasTranslations
{
    public function getTranslation(string $attribute, string $locale, bool $useFallback = true): string
    {
        // Prefer the dirty/set value over the database original
        $raw = $this->attributes[$attribute] ?? $this->getRawOriginal($attribute) ?? null;

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        if (! is_array($decoded)) {
            return (string) ($raw ?? '');
        }

        if (isset($decoded[$locale])) {
            return (string) $decoded[$locale];
        }

        if ($useFallback) {
            $fallback = config('app.fallback_locale', 'en');

            return (string) ($decoded[$fallback] ?? reset($decoded) ?? '');
        }

        return '';
    }

    public function setTranslation(string $attribute, string $locale, string $value): static
    {
        $raw = $this->attributes[$attribute] ?? $this->getRawOriginal($attribute) ?? null;

        $decoded = is_string($raw) ? (json_decode($raw, true) ?? []) : [];

        $decoded[$locale] = $value;

        $this->attributes[$attribute] = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return $this;
    }

    public function getTranslations(string $attribute): array
    {
        $raw = $this->attributes[$attribute] ?? $this->getRawOriginal($attribute) ?? null;

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        return is_array($decoded) ? $decoded : [];
    }
}

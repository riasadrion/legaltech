<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class DatabaseSettingsRepository
{
    private const CACHE_KEY = 'settings:autoload';

    private const CACHE_TTL = 3600;

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = $this->autoloaded();

            if (array_key_exists($key, $settings)) {
                return $settings[$key];
            }

            $setting = Setting::find($key);

            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            // If settings table doesn't exist, return default
            return $default;
        }
    }

    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general', bool $autoload = true): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            compact('value', 'type', 'group', 'autoload'),
        );

        $this->flush();
    }

    public function forget(string $key): void
    {
        Setting::destroy($key);
        $this->flush();
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, $default);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function integer(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function float(string $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }

    public function array(string $key, array $default = []): array
    {
        $value = $this->get($key, $default);

        return is_array($value) ? $value : $default;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function autoloaded(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            try {
                return Setting::where('autoload', true)
                    ->pluck('value', 'key')
                    ->toArray();
            } catch (\Exception $e) {
                // If settings table doesn't exist, return empty array
                return [];
            }
        });
    }
}

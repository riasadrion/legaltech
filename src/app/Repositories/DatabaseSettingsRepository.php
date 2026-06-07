<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class DatabaseSettingsRepository
{
    private const CACHE_KEY = 'settings:autoload';

    private const CACHE_TTL = 3600;

    private ?array $memo = null;

    private array $resolved = [];

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = $this->autoloaded();

            if (array_key_exists($key, $settings)) {
                return $settings[$key];
            }

            if (! array_key_exists($key, $this->resolved)) {
                $setting = Setting::find($key);
                $this->resolved[$key] = $setting ? $setting->value : null;
            }

            return $this->resolved[$key] ?? $default;
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
        $this->memo = null;
        $this->resolved = [];
        Cache::forget(self::CACHE_KEY);
    }

    private function autoloaded(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        return $this->memo = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
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

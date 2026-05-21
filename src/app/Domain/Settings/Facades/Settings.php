<?php

namespace App\Domain\Settings\Facades;

use App\Domain\Settings\Repositories\DatabaseSettingsRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value, string $type = 'string', string $group = 'general', bool $autoload = true)
 * @method static void forget(string $key)
 * @method static string string(string $key, string $default = '')
 * @method static bool bool(string $key, bool $default = false)
 * @method static int integer(string $key, int $default = 0)
 * @method static float float(string $key, float $default = 0.0)
 * @method static array array(string $key, array $default = [])
 * @method static void flush()
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DatabaseSettingsRepository::class;
    }
}

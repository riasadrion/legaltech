<?php

namespace App\Domain\Admin\Navigation;

use App\Models\Admin;

class AdminNavigation
{
    private static array $items = [];

    public static function register(array $items): void
    {
        foreach ($items as $item) {
            static::$items[$item['key']] = $item;
        }
    }

    /** @return array<int, array<string, mixed>> */
    public static function forUser(Admin $admin): array
    {
        $items = array_values(static::$items);

        usort($items, fn ($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

        return array_values(array_filter($items, function (array $item) use ($admin) {
            if (isset($item['permission']) && ! $admin->hasPermission($item['permission'])) {
                return false;
            }

            if (isset($item['children'])) {
                $item['children'] = array_values(array_filter(
                    $item['children'],
                    fn ($child) => ! isset($child['permission']) || $admin->hasPermission($child['permission'])
                ));
            }

            return true;
        }));
    }

    public static function all(): array
    {
        return static::$items;
    }

    public static function reset(): void
    {
        static::$items = [];
    }
}

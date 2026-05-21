<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\AdminNotification;

class AdminNotify
{
    public static function all(string $title, string $body = '', string $url = '', string $type = 'info'): void
    {
        $now = now();
        $rows = Admin::all()->map(fn($admin) => [
            'admin_id'   => $admin->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'read_at'    => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        AdminNotification::insert($rows);
    }
}

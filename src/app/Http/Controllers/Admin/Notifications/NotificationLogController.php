<?php

namespace App\Http\Controllers\Admin\Notifications;

use App\Domain\Notifications\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController
{
    public function index(Request $request): View
    {
        $logs = NotificationLog::with('notifiable')
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->channel))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('key'), fn ($q) => $q->where('notification_key', $request->key))
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $channels = NotificationLog::distinct()->pluck('channel');
        $keys = NotificationLog::distinct()->pluck('notification_key');

        return view('admin.notifications.logs.index', compact('logs', 'channels', 'keys'));
    }
}

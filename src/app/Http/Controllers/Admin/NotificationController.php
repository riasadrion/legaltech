<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $notifications = AdminNotification::where('admin_id', $admin->id)
            ->latest()
            ->paginate(25);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(AdminNotification $notification)
    {
        abort_if($notification->admin_id !== auth('admin')->id(), 403);

        $notification->read_at = now();
        $notification->save();

        return back();
    }

    public function markAllRead()
    {
        AdminNotification::where('admin_id', auth('admin')->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $count = AdminNotification::where('admin_id', auth('admin')->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function recent()
    {
        $items = AdminNotification::where('admin_id', auth('admin')->id())
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'title'    => $n->title,
                'body'     => $n->body,
                'url'      => $n->url,
                'unread'   => $n->isUnread(),
                'time'     => $n->created_at->diffForHumans(),
            ]);

        $unread = AdminNotification::where('admin_id', auth('admin')->id())
            ->whereNull('read_at')->count();

        return response()->json(['items' => $items, 'unread' => $unread]);
    }
}

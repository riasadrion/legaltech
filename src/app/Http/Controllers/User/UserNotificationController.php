<?php

namespace App\Http\Controllers\User;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserNotificationController
{
    public function index(): View
    {
        $user = auth('user')->user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(20);

        UserNotification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return view('theme::notifications.index', compact('notifications'));
    }

    public function markRead(UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === auth('user')->id(), 403);
        $notification->read_at = now();
        $notification->save();

        return redirect()->back();
    }

    public function markAllRead(): RedirectResponse
    {
        UserNotification::where('user_id', auth('user')->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount(): JsonResponse
    {
        $count = UserNotification::where('user_id', auth('user')->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}

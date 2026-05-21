<?php

namespace App\Http\Controllers\User;

use App\Domain\Notifications\Models\NotificationPreference;
use App\Domain\Notifications\Models\NotificationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationPreferenceController
{
    public function index(): View
    {
        $user = auth('user')->user();
        $templates = NotificationTemplate::active()->orderBy('name')->get();
        $prefs = NotificationPreference::where('user_id', $user->id)->get()
            ->keyBy(fn ($p) => $p->notification_key.'.'.$p->channel);

        return view('theme::notifications.preferences', compact('templates', 'prefs'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth('user')->user();
        $templates = NotificationTemplate::active()->get();

        foreach ($templates as $template) {
            foreach ($template->channels as $channel) {
                $enabled = $request->boolean("prefs.{$template->key}.{$channel}");

                $pref = NotificationPreference::where('user_id', $user->id)
                    ->where('notification_key', $template->key)
                    ->where('channel', $channel)
                    ->first();

                if ($pref === null) {
                    $pref = new NotificationPreference;
                    $pref->user_id = $user->id;
                    $pref->notification_key = $template->key;
                    $pref->channel = $channel;
                }

                $pref->enabled = $enabled;
                $pref->save();
            }
        }

        return redirect()->back()->with('success', 'Preferences saved.');
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user('user');

        $nudges = [];

        if (! $user->hasVerifiedEmail()) {
            $nudges[] = [
                'key' => 'verify_email',
                'title' => 'Verify your email address',
                'body' => 'Confirming your email unlocks all features.',
                'action' => route('verification.notice'),
                'label' => 'Verify email',
            ];
        }

        if (! $user->name) {
            $nudges[] = [
                'key' => 'add_name',
                'title' => 'Add your name',
                'body' => 'Help us personalise your experience.',
                'action' => route('user.profile'),
                'label' => 'Update profile',
            ];
        }

        if (! $user->two_factor_enabled) {
            $nudges[] = [
                'key' => 'setup_2fa',
                'title' => 'Set up two-factor authentication',
                'body' => 'Add an extra layer of security to your account.',
                'action' => route('user.2fa.index'),
                'label' => 'Enable 2FA',
            ];
        }

        $dismissed = session('dismissed_nudges', []);
        $nudges = array_filter($nudges, fn ($n) => ! in_array($n['key'], $dismissed));

        return view('user.dashboard', compact('nudges'));
    }

    public function dismissNudge(Request $request)
    {
        $key = $request->validate(['key' => ['required', 'string']])['key'];

        $dismissed = session('dismissed_nudges', []);
        $dismissed[] = $key;
        session(['dismissed_nudges' => $dismissed]);

        return response()->json(['ok' => true]);
    }
}

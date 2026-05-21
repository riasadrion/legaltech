<?php

namespace App\Http\Controllers;

use App\Domain\Notifications\Models\NotificationSuppression;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnsubscribeController
{
    public function show(Request $request): View
    {
        $email = $this->resolveEmail($request->query('token'));

        return view('theme::notifications.unsubscribe', compact('email'));
    }

    public function handle(Request $request): View|RedirectResponse
    {
        $email = $this->resolveEmail($request->input('token') ?? $request->query('token'));

        if (! $email) {
            abort(400, 'Invalid unsubscribe token.');
        }

        NotificationSuppression::suppress($email, NotificationSuppression::REASON_UNSUBSCRIBE, 'user');

        return view('theme::notifications.unsubscribed', compact('email'));
    }

    private function resolveEmail(?string $token): ?string
    {
        if (! $token) {
            return null;
        }

        $decoded = base64_decode($token, true);
        if (! $decoded) {
            return null;
        }

        [$email, $sig] = array_pad(explode('|', $decoded, 2), 2, '');
        $expected = hash_hmac('sha256', $email, config('app.key'));

        if (! hash_equals($expected, $sig)) {
            return null;
        }

        return $email;
    }
}

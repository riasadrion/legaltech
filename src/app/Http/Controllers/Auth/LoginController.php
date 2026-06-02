<?php

namespace App\Http\Controllers\Auth;

use App\Actions\LoginUser;
use App\Http\Controllers\Controller;
use App\Support\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('theme::auth.login');
    }

    public function store(Request $request, LoginUser $action)
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];

        if (Turnstile::enabled()) {
            $rules['cf-turnstile-response'] = ['required', function ($attr, $value, $fail) use ($request) {
                if (! Turnstile::verify($value, $request->ip())) {
                    $fail(__('Captcha verification failed. Please try again.'));
                }
            }];
        }

        $data = $request->validate($rules);
        $data = array_intersect_key($data, array_flip(['email', 'password']));

        if (! Auth::guard('user')->attempt($data, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('user')->user();

        if ($user->two_factor_enabled) {
            session(['2fa_user_id' => $user->id, '2fa_remember' => $request->boolean('remember')]);
            Auth::guard('user')->logout();

            return redirect()->route('2fa.challenge');
        }

        $action->logLogin($user, $request);

        return redirect()->intended(route('user.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->forget('admin_impersonating_user_id');
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

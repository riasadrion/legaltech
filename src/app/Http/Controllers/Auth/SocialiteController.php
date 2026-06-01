<?php

namespace App\Http\Controllers\Auth;

use App\Actions\HandleSocialLogin;
use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook', 'twitter', 'linkedin'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS), 404);
        abort_unless(Settings::string('social.'.$provider.'.enabled') === '1', 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request, HandleSocialLogin $action)
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Could not authenticate with '.ucfirst($provider).'. Please try again.']);
        }

        if (! $socialUser->getEmail()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No email address returned from '.ucfirst($provider).'. Please use email/password instead.']);
        }

        // Block new signups via social if registration is disabled (existing users can still log in)
        if (Settings::string('controls.registration_enabled', '1') !== '1'
            && ! User::where('email', $socialUser->getEmail())->exists()) {
            return redirect()->route('login')
                ->withErrors(['email' => __('Registration is currently disabled.')]);
        }

        $user = $action->execute($provider, $socialUser, $request);

        if (! $user->isActive()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended.']);
        }

        Auth::guard('user')->login($user, true);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }
}

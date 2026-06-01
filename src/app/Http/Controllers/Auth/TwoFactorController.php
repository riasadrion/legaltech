<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Actions\LoginUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function challenge()
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('theme::auth.two-factor-challenge');
    }

    public function verify(Request $request, Google2FA $google2fa, LoginUser $loginUser)
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = User::find(session('2fa_user_id'));

        if (! $user) {
            session()->forget(['2fa_user_id', '2fa_remember']);

            return redirect()->route('login');
        }

        $code = preg_replace('/\s+/', '', $request->code);

        $valid = $google2fa->verifyKey($user->two_factor_secret, $code, 1);

        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid authentication code. Please try again.']);
        }

        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::guard('user')->login($user, session('2fa_remember', false));
        $request->session()->regenerate();

        $loginUser->logLogin($user, $request);

        return redirect()->route('user.dashboard');
    }

    public function verifyRecovery(Request $request, LoginUser $loginUser)
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'recovery_code' => ['required', 'string'],
        ]);

        $user = User::find(session('2fa_user_id'));

        if (! $user) {
            session()->forget(['2fa_user_id', '2fa_remember']);

            return redirect()->route('login');
        }

        $codes = $user->two_factor_recovery_codes ?? [];
        $inputCode = trim($request->recovery_code);

        $matchedKey = null;
        foreach ($codes as $key => $code) {
            if (hash_equals($code, $inputCode)) {
                $matchedKey = $key;
                break;
            }
        }

        if ($matchedKey === null) {
            return back()->withErrors(['recovery_code' => 'Invalid recovery code.']);
        }

        unset($codes[$matchedKey]);
        $user->two_factor_recovery_codes = array_values($codes);
        $user->save();

        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::guard('user')->login($user, false);
        $request->session()->regenerate();

        $loginUser->logLogin($user, $request);

        return redirect()->route('user.dashboard')->with('warning', 'Recovery code used. Please generate new recovery codes.');
    }
}

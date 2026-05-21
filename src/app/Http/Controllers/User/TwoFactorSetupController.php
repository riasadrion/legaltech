<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Recovery\Recovery;

class TwoFactorSetupController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user('user');

        return view('user.2fa.index', [
            'enabled' => $user->two_factor_enabled,
        ]);
    }

    public function setup(Request $request, Google2FA $google2fa)
    {
        $user = $request->user('user');

        if ($user->two_factor_enabled) {
            return redirect()->route('user.2fa.index');
        }

        if (! session()->has('2fa_setup_secret')) {
            $secret = $google2fa->generateSecretKey();
            session(['2fa_setup_secret' => $secret]);
        }

        $secret = session('2fa_setup_secret');
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('user.2fa.setup', compact('secret', 'qrCodeUrl'));
    }

    public function enable(Request $request, Google2FA $google2fa, Recovery $recovery)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user('user');
        $secret = session('2fa_setup_secret');

        if (! $secret) {
            return redirect()->route('user.2fa.setup');
        }

        $code = preg_replace('/\s+/', '', $request->code);
        $valid = $google2fa->verifyKey($secret, $code, 1);

        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid code. Please scan the QR code again and retry.']);
        }

        $codes = $recovery->setCount(8)->setBlocks(2)->setChars(10)->toArray();

        $user->two_factor_enabled = true;
        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        session()->forget('2fa_setup_secret');

        return view('user.2fa.recovery-codes', compact('codes'));
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user('user');

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('user.2fa.index')->with('success', 'Two-factor authentication disabled.');
    }

    public function regenerateCodes(Request $request, Recovery $recovery)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user('user');

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $codes = $recovery->setCount(8)->setBlocks(2)->setChars(10)->toArray();
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        return view('user.2fa.recovery-codes', compact('codes'));
    }
}

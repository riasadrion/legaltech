<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\PhoneVerification\PhoneVerificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user('user');

        if ($user->mobile_verified_at) {
            return redirect()->route('user.dashboard');
        }

        return view('theme::auth.phone-verify', ['user' => $user]);
    }

    public function send(Request $request, PhoneVerificationService $service)
    {
        $data = $request->validate([
            'mobile_country_code' => ['required', 'string', 'max:10'],
            'mobile' => ['required', 'string', 'min:5', 'max:20'],
        ]);

        $phone = $data['mobile_country_code'].$data['mobile'];
        $user = $request->user('user');

        $result = $service->sendCode($user, $phone);

        if (! $result['sent']) {
            return back()->withErrors(['mobile' => $result['reason'] ?? 'Failed to send code.']);
        }

        // Save the entered phone so the verify view has it (not verified yet)
        $user->forceFill([
            'mobile_country_code' => $data['mobile_country_code'],
            'mobile' => $data['mobile'],
        ])->save();

        return back()->with('success', __('Verification code sent.'));
    }

    public function verify(Request $request, PhoneVerificationService $service)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user('user');

        if (! $service->verify($user, $data['code'])) {
            return back()->withErrors(['code' => __('Invalid or expired code.')]);
        }

        return redirect()->intended(route('user.dashboard'))->with('success', __('Phone number verified.'));
    }
}

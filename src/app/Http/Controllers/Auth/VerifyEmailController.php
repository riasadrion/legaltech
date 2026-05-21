<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('user.dashboard')->with('success', 'Email verified successfully.');
    }

    public function resend(Request $request)
    {
        if ($request->user('user')->hasVerifiedEmail()) {
            return redirect()->route('user.dashboard');
        }

        $request->user('user')->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function show()
    {
        return view('admin.auth.admin-login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('admin')->attempt($data, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();

        if (! $admin->isActive()) {
            Auth::guard('admin')->logout();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account has been suspended.']);
        }

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request)
    {
        // Log out only the admin guard. Calling session()->invalidate() here would
        // destroy the whole session and forcibly log out the user guard too,
        // breaking simultaneous admin + user sessions in the same browser.
        Auth::guard('admin')->logout();
        $request->session()->forget('admin_impersonating_user_id');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

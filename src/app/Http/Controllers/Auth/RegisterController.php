<?php

namespace App\Http\Controllers\Auth;

use App\Actions\RegisterUser;
use App\Support\RegistrationFields;
use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\VerifyEmail;
use App\Support\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function show()
    {
        $this->abortIfDisabled();

        return view('theme::auth.register', [
            'extraFields' => RegistrationFields::all(),
        ]);
    }

    public function store(Request $request, RegisterUser $action)
    {
        $this->abortIfDisabled();
        $rules = array_merge([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'name' => ['nullable', 'string', 'max:255'],
        ], RegistrationFields::validationRules());

        if (Settings::string('controls.policy_agreement') === '1') {
            $rules['policy_agreement'] = ['accepted'];
        }

        if (Turnstile::enabled()) {
            $rules['cf-turnstile-response'] = ['required', function ($attr, $value, $fail) use ($request) {
                if (! Turnstile::verify($value, $request->ip())) {
                    $fail(__('Captcha verification failed. Please try again.'));
                }
            }];
        }

        $data = $request->validate($rules);
        unset($data['cf-turnstile-response'], $data['policy_agreement']);

        $user = $action->execute($data, $request);

        Auth::guard('user')->login($user);

        $request->session()->regenerate();

        $user->notify(new VerifyEmail);

        return redirect()->route('user.dashboard');
    }

    private function abortIfDisabled(): void
    {
        if (Settings::string('controls.registration_enabled', '1') !== '1') {
            abort(Response::HTTP_FORBIDDEN, __('Registration is currently disabled.'));
        }
    }
}

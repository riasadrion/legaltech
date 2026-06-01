<?php

namespace App\Http\Controllers\User;

use App\Services\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user('user');
        $countries = $this->countryList();

        return view('theme::user.profile', compact('user', 'countries'));
    }

    public function update(Request $request)
    {
        $user = $request->user('user');

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'username'           => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'mobile_country_code' => ['nullable', 'string', 'max:10'],
            'mobile'             => ['nullable', 'string', 'max:20'],
            'country_code'       => ['nullable', 'string', 'size:2'],
            'address'            => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:100'],
            'state'              => ['nullable', 'string', 'max:100'],
            'zip'                => ['nullable', 'string', 'max:20'],
            'timezone'           => ['nullable', 'timezone'],
            'avatar'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('uploads')->delete($user->avatar_path);
            }
            $uploader = app(FileUploader::class);
            $meta = $uploader->store($request->file('avatar'), 'users', $user->id);
            $data['avatar_path'] = $meta->url;
        }

        unset($data['avatar']);
        $user->fill($data)->save();

        return back()->with('success', __('Profile updated.'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user('user');

        $request->validate([
            'current_password' => ['required', 'string', function ($_attr, $val, $fail) use ($user) {
                if (! Hash::check($val, $user->password)) {
                    $fail(__('Current password is incorrect.'));
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->password = Hash::make($request->password);
        $user->setRememberToken(\Illuminate\Support\Str::random(60));
        $user->save();

        // Re-login current session so this device stays authenticated
        auth('user')->login($user);

        return back()->with('success', __('Password updated. All other sessions have been signed out.'));
    }

    private function countryList(): array
    {
        return [
            'AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AR' => 'Argentina',
            'AU' => 'Australia', 'AT' => 'Austria', 'BD' => 'Bangladesh', 'BE' => 'Belgium',
            'BR' => 'Brazil', 'CA' => 'Canada', 'CL' => 'Chile', 'CN' => 'China',
            'CO' => 'Colombia', 'HR' => 'Croatia', 'CZ' => 'Czech Republic', 'DK' => 'Denmark',
            'EG' => 'Egypt', 'FI' => 'Finland', 'FR' => 'France', 'DE' => 'Germany',
            'GH' => 'Ghana', 'GR' => 'Greece', 'HU' => 'Hungary', 'IN' => 'India',
            'ID' => 'Indonesia', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy',
            'JP' => 'Japan', 'KE' => 'Kenya', 'MY' => 'Malaysia', 'MX' => 'Mexico',
            'MA' => 'Morocco', 'NL' => 'Netherlands', 'NZ' => 'New Zealand', 'NG' => 'Nigeria',
            'NO' => 'Norway', 'PK' => 'Pakistan', 'PE' => 'Peru', 'PH' => 'Philippines',
            'PL' => 'Poland', 'PT' => 'Portugal', 'RO' => 'Romania', 'RU' => 'Russia',
            'SA' => 'Saudi Arabia', 'SG' => 'Singapore', 'ZA' => 'South Africa', 'KR' => 'South Korea',
            'ES' => 'Spain', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'TW' => 'Taiwan',
            'TH' => 'Thailand', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States',
            'UY' => 'Uruguay', 'VN' => 'Vietnam',
        ];
    }

    public function destroy(Request $request)
    {
        $user = $request->user('user');

        $request->validate([
            'delete_password' => ['required', 'string', function ($_attr, $val, $fail) use ($user) {
                if (! Hash::check($val, $user->password)) {
                    $fail(__('Password is incorrect.'));
                }
            }],
        ]);

        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user->avatar_path) {
            Storage::disk('uploads')->delete($user->avatar_path);
        }

        $user->delete();

        return redirect('/')->with('success', __('Your account and all associated data have been permanently deleted.'));
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user('user');

        if ($user->avatar_path) {
            Storage::disk('uploads')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return back()->with('success', __('Avatar removed.'));
    }
}

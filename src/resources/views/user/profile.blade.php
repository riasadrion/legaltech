@extends('theme::layouts.user')

@section('breadcrumb', __('Profile'))

@section('content')
@php
    $user = auth('user')->user();
@endphp

<div class="space-y-8 max-w-2xl">

    {{-- Page heading --}}
    <div>
        <h1 class="ud-page-title">{{ __('Profile Settings') }}</h1>
        <p class="ud-page-sub">{{ __('Manage your personal information and account security.') }}</p>
    </div>

    {{-- ── Avatar & basic info ── --}}
    <div class="ud-card">
        <div class="ud-card-header">
            <div>
                <div class="ud-section-title">{{ __('Personal Information') }}</div>
                <div class="ud-section-desc">{{ __('Update your name, username, and profile photo.') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="flex items-center gap-5 mb-6">
                <div class="ud-avatar-wrap">
                    @if($user->avatar_path)
                        <img src="{{ upload_url($user->avatar_path) }}" alt="{{ $user->name }}" class="ud-avatar" id="avatar-preview">
                    @else
                        <div class="ud-avatar-initials" id="avatar-initials">
                            {{ mb_strtoupper(mb_substr($user->name ?? $user->email, 0, 1)) }}
                        </div>
                        <img src="" alt="" class="ud-avatar hidden" id="avatar-preview">
                    @endif
                    <label for="avatar-input" class="ud-avatar-upload-btn" title="{{ __('Change photo') }}">
                        <x-heroicon-o-camera class="h-3 w-3" />
                    </label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>
                <div>
                    <div class="text-sm font-medium text-white">{{ $user->name ?? __('No name set') }}</div>
                    <div class="text-xs text-white/45 mt-0.5">{{ $user->email }}</div>
                    @if($user->avatar_path)
                    <form method="POST" action="{{ route('user.profile.avatar.delete') }}" class="inline mt-2 block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300">{{ __('Remove photo') }}</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Name + username --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="ud-label" for="name">{{ __('Full name') }} <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="ud-input @error('name') border-red-500/50 @enderror" autocomplete="name">
                    @error('name') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ud-label" for="username">{{ __('Username') }}</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                           class="ud-input @error('username') border-red-500/50 @enderror" autocomplete="username">
                    @error('username') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Contact --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
                <div>
                    <label class="ud-label" for="mobile">{{ __('Phone number') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="mobile_country_code" value="{{ old('mobile_country_code', $user->mobile_country_code) }}"
                               placeholder="+1" class="ud-input w-20 flex-shrink-0">
                        <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                               class="ud-input flex-1 @error('mobile') border-red-500/50 @enderror">
                    </div>
                    @error('mobile') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ud-label" for="timezone">{{ __('Timezone') }}</label>
                    <select id="timezone" name="timezone" class="ud-input ud-select @error('timezone') border-red-500/50 @enderror">
                        <option value="">— {{ __('Select timezone') }} —</option>
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}" @selected(old('timezone', $user->timezone) === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Address --}}
            <div class="mt-4">
                <label class="ud-label" for="address">{{ __('Address') }}</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}"
                       class="ud-input @error('address') border-red-500/50 @enderror" placeholder="{{ __('Street address') }}">
                @error('address') <div class="ud-field-err">{{ $message }}</div> @enderror
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
                <div>
                    <label class="ud-label" for="city">{{ __('City') }}</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" class="ud-input">
                </div>
                <div>
                    <label class="ud-label" for="state">{{ __('State / Region') }}</label>
                    <input type="text" id="state" name="state" value="{{ old('state', $user->state) }}" class="ud-input">
                </div>
                <div>
                    <label class="ud-label" for="zip">{{ __('ZIP / Postal') }}</label>
                    <input type="text" id="zip" name="zip" value="{{ old('zip', $user->zip) }}" class="ud-input">
                </div>
            </div>
            <div class="mt-4">
                <label class="ud-label" for="country_code">{{ __('Country') }}</label>
                <select id="country_code" name="country_code" class="ud-input ud-select">
                    <option value="">— {{ __('Select country') }} —</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" @selected(old('country_code', $user->country_code) === $code)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="ud-btn-primary">{{ __('Save changes') }}</button>
                @if(session('success'))
                    <span class="text-sm text-lime">{{ session('success') }}</span>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Email (read-only + verified badge) ── --}}
    <div class="ud-card">
        <div class="ud-card-header">
            <div>
                <div class="ud-section-title">{{ __('Email Address') }}</div>
                <div class="ud-section-desc">{{ __('Your login email. Contact support to change it.') }}</div>
            </div>
            @if($user->hasVerifiedEmail())
                <span class="ud-badge ud-badge-green">{{ __('Verified') }}</span>
            @else
                <span class="ud-badge ud-badge-amber">{{ __('Unverified') }}</span>
            @endif
        </div>
        <input type="email" value="{{ $user->email }}" class="ud-input" disabled>
        @if(!$user->hasVerifiedEmail())
        <p class="text-xs text-white/45 mt-2">
            {{ __('Check your inbox or') }}
            <a href="{{ route('verification.notice') }}" class="ud-card-link">{{ __('resend verification email') }}</a>.
        </p>
        @endif
    </div>

    {{-- ── Password ── --}}
    <div class="ud-card">
        <div class="ud-card-header">
            <div>
                <div class="ud-section-title">{{ __('Change Password') }}</div>
                <div class="ud-section-desc">{{ __('Use a strong password with uppercase, lowercase, and numbers.') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.profile.password') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="ud-label" for="current_password">{{ __('Current password') }}</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                           class="ud-input @error('current_password') border-red-500/50 @enderror">
                    @error('current_password') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ud-label" for="password">{{ __('New password') }}</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                           class="ud-input @error('password') border-red-500/50 @enderror">
                    @error('password') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ud-label" for="password_confirmation">{{ __('Confirm new password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                           class="ud-input">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="ud-btn-primary">{{ __('Update password') }}</button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
$(function () {
    'use strict';
    $('#avatar-input').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#avatar-preview').attr('src', e.target.result).removeClass('hidden');
            $('#avatar-initials').addClass('hidden');
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush

@endsection

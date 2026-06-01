@extends('theme::layouts.user')

@section('breadcrumb', __('Profile'))

@section('content')
@php
    $user = auth('user')->user();
@endphp

<div class="space-y-6">


    <div>
        <h1 class="ud-page-title">@lang('Profile Settings')</h1>
        <p class="ud-page-sub">@lang('Manage your personal information and account security.')</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


    <div class="ud-card lg:col-span-2">
        <div class="ud-card-header">
            <div>
                <div class="ud-section-title">@lang('Personal Information')</div>
                <div class="ud-section-desc">@lang('Update your name, username, and profile photo.')</div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')


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
                    <label for="avatar-input" class="ud-avatar-upload-btn" :title="__('Change photo')">
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
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300">@lang('Remove photo')</button>
                    </form>
                    @endif
                </div>
            </div>


            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="ud-label" for="name">@lang('Full name') <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="ud-input @error('name') border-red-500/50 @enderror" autocomplete="name">
                    @error('name') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ud-label" for="username">@lang('Username')</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                           class="ud-input @error('username') border-red-500/50 @enderror" autocomplete="username">
                    @error('username') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
            </div>


            <div class="mt-4">
                <label class="ud-label">@lang('Phone number')</label>
                <x-phone-input
                    name="mobile"
                    code-name="mobile_country_code"
                    :code-value="old('mobile_country_code', $user->mobile_country_code ?? '')"
                    :mobile-value="old('mobile', $user->mobile ?? '')"
                />
                @error('mobile') <div class="ud-field-err">{{ $message }}</div> @enderror
            </div>


            <div class="mt-4">
                <label class="ud-label" for="address">@lang('Address')</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}"
                       class="ud-input @error('address') border-red-500/50 @enderror" :placeholder="__('Street address')">
                @error('address') <div class="ud-field-err">{{ $message }}</div> @enderror
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
                <div>
                    <label class="ud-label" for="city">@lang('City')</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" class="ud-input">
                </div>
                <div>
                    <label class="ud-label" for="state">@lang('State / Region')</label>
                    <input type="text" id="state" name="state" value="{{ old('state', $user->state) }}" class="ud-input">
                </div>
                <div>
                    <label class="ud-label" for="zip">@lang('ZIP / Postal')</label>
                    <input type="text" id="zip" name="zip" value="{{ old('zip', $user->zip) }}" class="ud-input">
                </div>
            </div>
            <div class="mt-4">
                <label class="ud-label" for="country_code">@lang('Country')</label>
                <select id="country_code" name="country_code" class="ud-select">
                    <option value="">— @lang('Select country') —</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" @selected(old('country_code', $user->country_code) === $code)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="ud-btn-primary">@lang('Save changes')</button>
                @if(session('success'))
                    <span class="text-sm text-lime">{{ session('success') }}</span>
                @endif
            </div>
        </form>
    </div>


    <div class="space-y-6">


        <div class="ud-card">
            <div class="ud-card-header">
                <div>
                    <div class="ud-section-title">@lang('Email Address')</div>
                    <div class="ud-section-desc">@lang('Your login email.')</div>
                </div>
                @if($user->hasVerifiedEmail())
                    <span class="ud-badge ud-badge-green">@lang('Verified')</span>
                @else
                    <span class="ud-badge ud-badge-amber">@lang('Unverified')</span>
                @endif
            </div>
            <input type="email" value="{{ $user->email }}" class="ud-input" disabled>
            @if(!$user->hasVerifiedEmail())
            <p class="text-xs text-white/45 mt-2">
                @lang('Check your inbox or')
                <a href="{{ route('verification.notice') }}" class="ud-card-link">@lang('resend verification email')</a>.
            </p>
            @endif
        </div>


        <div class="ud-card">
            <div class="ud-card-header">
                <div>
                    <div class="ud-section-title">@lang('Change Password')</div>
                    <div class="ud-section-desc">@lang('Use a strong, unique password.')</div>
                </div>
            </div>

            <form method="POST" action="{{ route('user.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="ud-label" for="current_password">@lang('Current password')</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                               class="ud-input @error('current_password') border-red-500/50 @enderror">
                        @error('current_password') <div class="ud-field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="ud-label" for="password">@lang('New password')</label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               class="ud-input @error('password') border-red-500/50 @enderror">
                        @error('password') <div class="ud-field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="ud-label" for="password_confirmation">@lang('Confirm new password')</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="ud-input">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="ud-btn-primary">@lang('Update password')</button>
                </div>
            </form>
        </div>

    </div>

    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="ud-card border border-red-500/20 lg:col-span-2">
        <div class="ud-card-header">
            <div>
                <div class="ud-section-title text-red-400">@lang('Delete Account')</div>
                <div class="ud-section-desc">@lang('Permanently delete your account and all associated data. This action cannot be undone.')</div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.profile.destroy') }}" id="delete-account-form">
            @csrf
            @method('DELETE')
            <div class="space-y-4">
                <div>
                    <label class="ud-label" for="delete_password">@lang('Confirm your password to continue')</label>
                    <input type="password" id="delete_password" name="delete_password" autocomplete="current-password"
                           class="ud-input @error('delete_password') border-red-500/50 @enderror"
                           :placeholder="__('Enter your password')">
                    @error('delete_password') <div class="ud-field-err">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="button" id="delete-account-btn"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    <x-heroicon-o-trash class="h-4 w-4" />
                    @lang('Delete My Account')
                </button>
            </div>
        </form>
    </div>
    </div>

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

    $('#delete-account-btn').on('click', function () {
        if (!$('#delete_password').val().trim()) {
            alert('@lang("Please enter your password to confirm.")');
            return;
        }
        confirmModal.show(
            '@lang("Delete Account")',
            '@lang("This will permanently delete your account and all associated data. This cannot be undone.")',
            function () { $('#delete-account-form').submit(); }
        );
    });
});
</script>
@endpush

@endsection

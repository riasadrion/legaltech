@extends('theme::layouts.auth')

@section('heading', __('Create account'))
@section('subheading', __('Get started in seconds. No credit card required.'))

@section('content')

@if($errors->any())
    <div class="auth-alert auth-alert-err mb-5">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('register.store') }}" class="space-y-4">
    @csrf

    <div class="auth-field-wrap">
        <label class="auth-label" for="name">{{ __('Name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}"
               placeholder="{{ __('Your name') }}" autocomplete="name"
               class="auth-input {{ $errors->has('name') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="email">{{ __('Email address') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}"
               placeholder="you@example.com" autocomplete="email" required
               class="auth-input {{ $errors->has('email') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="password">{{ __('Password') }}</label>
        <input id="password" name="password" type="password"
               placeholder="{{ __('Min 8 characters') }}" autocomplete="new-password" required
               class="auth-input {{ $errors->has('password') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="password_confirmation">{{ __('Confirm password') }}</label>
        <input id="password_confirmation" name="password_confirmation" type="password"
               placeholder="{{ __('Repeat password') }}" autocomplete="new-password" required
               class="auth-input">
    </div>

    @foreach($extraFields as $field)
    @if(($field['type'] ?? 'text') === 'hidden' || $field['key'] === 'mobile_country_code')
        @continue
    @endif
    <div class="auth-field-wrap">
        <label class="auth-label" for="{{ $field['key'] }}">{{ $field['label'] }}</label>
        @if($field['key'] === 'mobile')
            <x-phone-input
                name="mobile"
                code-name="mobile_country_code"
                :code-value="old('mobile_country_code', '')"
                :mobile-value="old('mobile', '')"
                :required="$field['required'] ?? false"
                input-class="{{ $errors->has('mobile') ? 'auth-input-err' : '' }}"
            />
        @else
            <input id="{{ $field['key'] }}" name="{{ $field['key'] }}"
                   type="{{ $field['type'] ?? 'text' }}" value="{{ old($field['key']) }}"
                   {{ ($field['required'] ?? false) ? 'required' : '' }}
                   class="auth-input {{ $errors->has($field['key']) ? 'auth-input-err' : '' }}">
        @endif
        @error($field['key'])
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
    @endforeach

    @if(setting('controls.policy_agreement') === '1')
    <div>
        <label class="flex items-start gap-2.5 cursor-pointer">
            <input type="checkbox" name="policy_agreement" value="1"
                   {{ old('policy_agreement') ? 'checked' : '' }} required
                   class="auth-checkbox mt-0.5">
            <span class="text-sm text-white/55 leading-snug">
                {{ __('I agree to the') }}
                <a href="{{ route('page.show', 'terms') }}" target="_blank" class="auth-link">{{ __('Terms') }}</a>
                {{ __('and') }}
                <a href="{{ route('page.show', 'privacy-policy') }}" target="_blank" class="auth-link">{{ __('Privacy Policy') }}</a>.
            </span>
        </label>
        @error('policy_agreement')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
    @endif

    <x-turnstile />

    <button type="submit" class="auth-btn-primary w-full">
        {{ __('Create account') }}
    </button>
</form>

@php
$anySocial = collect(['google','facebook','twitter','linkedin'])
    ->contains(fn($p) => setting('social.'.$p.'.enabled') === '1' && setting('social.'.$p.'.client_id') !== '');
@endphp
@if($anySocial)
<div class="auth-divider">
    <span>{{ __('Or continue with') }}</span>
</div>
@include('auth.partials.social-buttons')
@endif

<p class="mt-6 text-center text-sm text-white/50">
    {{ __('Already have an account?') }}
    <a href="{{ route('login') }}" class="auth-link font-medium">{{ __('Sign in') }}</a>
</p>

@endsection

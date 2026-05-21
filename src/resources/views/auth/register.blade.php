@extends('theme::layouts.auth')

@section('heading', 'Create your account')

@section('content')
<form method="POST" action="{{ route('register.store') }}" class="space-y-5">
    @csrf

    <x-form.input
        name="name"
        label="Name"
        type="text"
        :value="old('name')"
        placeholder="Your name (optional)"
        autocomplete="name"
    />

    <x-form.input
        name="email"
        label="Email address"
        type="email"
        :value="old('email')"
        placeholder="you@example.com"
        autocomplete="email"
        required
    />

    <x-form.input
        name="password"
        label="Password"
        type="password"
        placeholder="Min 8 characters"
        autocomplete="new-password"
        required
    />

    <x-form.input
        name="password_confirmation"
        label="Confirm password"
        type="password"
        placeholder="Repeat password"
        autocomplete="new-password"
        required
    />

    @foreach ($extraFields as $field)
        <x-form.input
            :name="$field['key']"
            :label="$field['label']"
            :type="$field['type'] ?? 'text'"
            :value="old($field['key'])"
            :required="$field['required'] ?? false"
        />
    @endforeach

    @if(setting('controls.policy_agreement') === '1')
    <div>
        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="policy_agreement" value="1" {{ old('policy_agreement') ? 'checked' : '' }} required
                   class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span>
                {{ __('I agree to the') }}
                <a href="/terms" target="_blank" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Terms') }}</a>
                {{ __('and') }}
                <a href="/privacy" target="_blank" class="text-brand-600 hover:text-brand-700 font-medium">{{ __('Privacy Policy') }}</a>.
            </span>
        </label>
        @error('policy_agreement')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @endif

    <x-turnstile />

    <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        Create account
    </button>
</form>

@php
$anySocial = collect(['google','facebook','twitter','linkedin'])
    ->contains(fn($p) => setting('social.'.$p.'.enabled') === '1' && setting('social.'.$p.'.client_id') !== '');
@endphp
@if($anySocial)
<div class="mt-6">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-3 text-gray-500">Or continue with</span>
        </div>
    </div>
    @include('auth.partials.social-buttons')
</div>
@endif

<p class="mt-6 text-center text-sm text-gray-500">
    Already have an account?
    <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Sign in</a>
</p>
@endsection

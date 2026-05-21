@extends('theme::layouts.auth')

@section('heading', 'Sign in to your account')

@section('content')
@if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login.store') }}" class="space-y-5">
    @csrf

    <x-form.input
        name="email"
        label="Email address"
        type="email"
        :value="old('email')"
        placeholder="you@example.com"
        autocomplete="email"
        required
    />

    <div>
        <x-form.input
            name="password"
            label="Password"
            type="password"
            placeholder="Your password"
            autocomplete="current-password"
            required
        />
        <div class="mt-1 text-end">
            <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700">Forgot password?</a>
        </div>
    </div>

    <x-form.checkbox name="remember" label="Remember me" />

    <x-turnstile />

    <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        Sign in
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

@if(setting('controls.registration_enabled', '1') === '1')
<p class="mt-6 text-center text-sm text-gray-500">
    Don't have an account?
    <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-700">Sign up</a>
</p>
@endif
@endsection

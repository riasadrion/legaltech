@extends('theme::layouts.auth')

@section('heading', __('Sign in'))
@section('subheading', __('Welcome back. Enter your details to continue.'))

@section('content')

@if(session('status'))
    <div class="auth-alert auth-alert-ok">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="auth-alert auth-alert-err mb-5">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('login.store') }}" class="space-y-4">
    @csrf

    <div class="auth-field-wrap">
        <label class="auth-label" for="email">@lang('Email address')</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}"
               placeholder="you@example.com" autocomplete="email" required
               class="auth-input {{ $errors->has('email') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <div class="flex items-center justify-between mb-1.5">
            <label class="auth-label mb-0" for="password">@lang('Password')</label>
            <a href="{{ route('password.request') }}" class="auth-link text-xs">@lang('Forgot password?')</a>
        </div>
        <div class="relative" data-pwd-wrap>
            <input id="password" name="password" type="password"
                   placeholder="••••••••" autocomplete="current-password" required
                   class="auth-input pe-11 {{ $errors->has('password') ? 'auth-input-err' : '' }}">
            <button type="button" data-pwd-toggle tabindex="-1"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-ink-400 hover:text-ink-700">
                <x-heroicon-o-eye class="h-5 w-5" data-eye />
                <x-heroicon-o-eye-slash class="h-5 w-5 hidden" data-eye-off />
            </button>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input id="remember" name="remember" type="checkbox"
               class="auth-checkbox" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="text-sm text-ink-500 cursor-pointer">@lang('Remember me')</label>
    </div>

    <x-turnstile />

    <button type="submit" class="btn-primary w-full flex items-center justify-center rounded-full px-5 py-3.5 text-[15px] font-semibold">
        @lang('Sign in')
    </button>
</form>

@php
$anySocial = collect(['google','facebook','twitter','linkedin'])
    ->contains(fn($p) => setting('social.'.$p.'.enabled') === '1' && setting('social.'.$p.'.client_id') !== '');
@endphp
@if($anySocial)
<div class="auth-divider">
    <span>@lang('Or continue with')</span>
</div>
@include('theme::auth.partials.social-buttons')
@endif

@if(setting('controls.registration_enabled', '1') === '1')
<p class="mt-6 text-center text-sm text-ink-500">
    @lang("Don't have an account?")
    <a href="{{ route('register') }}" class="auth-link font-medium">@lang('Sign up')</a>
</p>
@endif

@endsection

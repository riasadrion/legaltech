@extends('theme::layouts.auth')

@section('heading', __('Set new password'))
@section('subheading', __('Choose a strong password for your account.'))

@section('content')

@if($errors->any())
    <div class="auth-alert auth-alert-err mb-5">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="auth-field-wrap">
        <label class="auth-label" for="email">@lang('Email address')</label>
        <input id="email" name="email" type="email" value="{{ $email ?? old('email') }}"
               placeholder="you@example.com" autocomplete="email" required
               class="auth-input {{ $errors->has('email') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="password">@lang('New password')</label>
        <div class="relative" data-pwd-wrap>
            <input id="password" name="password" type="password"
                   placeholder="{{ __('Min 8 characters') }}" autocomplete="new-password" required
                   class="auth-input pe-11 {{ $errors->has('password') ? 'auth-input-err' : '' }}">
            <button type="button" data-pwd-toggle tabindex="-1"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-ink-400 hover:text-ink-700">
                <x-heroicon-o-eye class="h-5 w-5" data-eye />
                <x-heroicon-o-eye-slash class="h-5 w-5 hidden" data-eye-off />
            </button>
        </div>
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="password_confirmation">@lang('Confirm new password')</label>
        <div class="relative" data-pwd-wrap>
            <input id="password_confirmation" name="password_confirmation" type="password"
                   placeholder="{{ __('Repeat password') }}" autocomplete="new-password" required
                   class="auth-input pe-11">
            <button type="button" data-pwd-toggle tabindex="-1"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-ink-400 hover:text-ink-700">
                <x-heroicon-o-eye class="h-5 w-5" data-eye />
                <x-heroicon-o-eye-slash class="h-5 w-5 hidden" data-eye-off />
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-full flex items-center justify-center rounded-xl px-5 py-3.5 text-[15px] font-semibold">
        @lang('Reset password')
    </button>
</form>

@endsection

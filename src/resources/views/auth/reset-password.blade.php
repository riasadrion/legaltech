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
        <input id="password" name="password" type="password"
               placeholder="@lang('Min 8 characters')" autocomplete="new-password" required
               class="auth-input {{ $errors->has('password') ? 'auth-input-err' : '' }}">
    </div>

    <div class="auth-field-wrap">
        <label class="auth-label" for="password_confirmation">@lang('Confirm new password')</label>
        <input id="password_confirmation" name="password_confirmation" type="password"
               placeholder="@lang('Repeat password')" autocomplete="new-password" required
               class="auth-input">
    </div>

    <button type="submit" class="auth-btn-primary w-full">
        @lang('Reset password')
    </button>
</form>

@endsection

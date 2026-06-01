@extends('theme::layouts.auth')

@section('heading', __('Forgot password?'))
@section('subheading', __("Enter your email and we'll send you a reset link."))

@section('content')

@if(session('status'))
    <div class="auth-alert auth-alert-ok">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="auth-alert auth-alert-err mb-5">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div class="auth-field-wrap">
        <label class="auth-label" for="email">@lang('Email address')</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}"
               placeholder="you@example.com" autocomplete="email" required
               class="auth-input {{ $errors->has('email') ? 'auth-input-err' : '' }}">
    </div>

    <button type="submit" class="auth-btn-primary w-full">
        @lang('Send reset link')
    </button>
</form>

<p class="mt-6 text-center text-sm text-white/50">
    <a href="{{ route('login') }}" class="auth-link">@lang('← Back to sign in')</a>
</p>

@endsection

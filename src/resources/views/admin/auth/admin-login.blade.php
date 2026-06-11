@extends('admin.layouts.auth')

@section('title', 'Admin Login')
@section('heading', 'Welcome back 👋')
@section('subheading', 'Sign in to your admin account to continue.')

@section('content')
<form method="POST" action="{{ route('admin.login.store') }}">
    @csrf

    <div class="al-field">
        <label class="al-label" for="email">Email address</label>
        <input id="email" name="email" type="email" class="al-input" value="{{ old('email') }}"
               placeholder="admin@example.com" autocomplete="email" required>
    </div>

    <div class="al-field">
        <label class="al-label" for="password">Password</label>
        <div class="al-input-wrap">
            <input id="password" name="password" type="password" class="al-input al-input-pwd"
                   placeholder="Your password" autocomplete="current-password" required>
            <button type="button" class="al-eye" id="togglePwd" aria-label="Toggle password">
                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="al-row">
        <label class="al-check">
            <input type="checkbox" name="remember"> Remember me
        </label>
        <a href="{{ route('admin.password.request') }}" class="al-forgot">Forgot password?</a>
    </div>

    <button type="submit" class="al-btn">Sign in to Dashboard</button>
</form>

<div class="al-divider"><span>secured</span></div>
@endsection

@push('scripts')
<script>
$(function () {
    'use strict';

    var $input = $('#password');
    var $icon = $('#eyeIcon');
    var eyeOff = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    var eyeOn = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';

    $('#togglePwd').on('click', function () {
        var isText = $input.attr('type') === 'text';
        $input.attr('type', isText ? 'password' : 'text');
        $icon.html(isText ? eyeOn : eyeOff);
    });
});
</script>
@endpush

@extends('admin.layouts.auth')

@section('title', 'Set new admin password')
@section('heading', 'Set new admin password')
@section('subheading', 'Choose a strong new password for your admin account.')

@section('content')
<form method="POST" action="{{ route('admin.password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="al-field">
        <label class="al-label" for="email">Email address</label>
        <input id="email" name="email" type="email" class="al-input" value="{{ $email ?? old('email') }}"
               placeholder="admin@example.com" autocomplete="email" required>
    </div>

    <div class="al-field">
        <label class="al-label" for="password">New password</label>
        <div class="al-input-wrap">
            <input id="password" name="password" type="password" class="al-input al-input-pwd"
                   placeholder="Min 8 characters" autocomplete="new-password" required>
            <button type="button" class="al-eye" data-pwd-toggle aria-label="Toggle password">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="al-field">
        <label class="al-label" for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="al-input"
               placeholder="Repeat password" autocomplete="new-password" required>
    </div>

    <button type="submit" class="al-btn">Reset password</button>
</form>

<a href="{{ route('admin.login') }}" class="al-back">← Back to sign in</a>
@endsection

@push('scripts')
<script>
$(function () {
    'use strict';

    $('[data-pwd-toggle]').on('click', function () {
        var $input = $(this).closest('.al-input-wrap').find('input');
        $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
    });
});
</script>
@endpush

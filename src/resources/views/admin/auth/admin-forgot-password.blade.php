@extends('admin.layouts.auth')

@section('title', 'Reset admin password')
@section('heading', 'Reset admin password')
@section('subheading', "Enter your admin email and we'll send you a reset link.")

@section('content')
<form method="POST" action="{{ route('admin.password.email') }}">
    @csrf

    <div class="al-field">
        <label class="al-label" for="email">Email address</label>
        <input id="email" name="email" type="email" class="al-input" value="{{ old('email') }}"
               placeholder="admin@example.com" autocomplete="email" required>
    </div>

    <button type="submit" class="al-btn">Send reset link</button>
</form>

<a href="{{ route('admin.login') }}" class="al-back">← Back to sign in</a>
@endsection

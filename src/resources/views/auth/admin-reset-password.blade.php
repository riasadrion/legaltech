@extends('theme::layouts.auth')

@section('heading', 'Set new admin password')

@section('content')
<form method="POST" action="{{ route('admin.password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <x-form.input
        name="email"
        label="Email address"
        type="email"
        :value="$email ?? old('email')"
        placeholder="admin@example.com"
        autocomplete="email"
        required
    />

    <x-form.input
        name="password"
        label="New password"
        type="password"
        placeholder="Min 8 characters"
        autocomplete="new-password"
        required
    />

    <x-form.input
        name="password_confirmation"
        label="Confirm new password"
        type="password"
        placeholder="Repeat password"
        autocomplete="new-password"
        required
    />

    <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        Reset password
    </button>
</form>
@endsection

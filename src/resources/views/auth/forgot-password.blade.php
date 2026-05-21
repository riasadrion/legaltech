@extends('theme::layouts.auth')

@section('heading', 'Reset your password')

@section('content')
@if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
@endif

<p class="mb-5 text-sm text-gray-500 text-center">
    Enter your email and we'll send you a link to reset your password.
</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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

    <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        Send reset link
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-500">
    <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Back to sign in</a>
</p>
@endsection

@extends('theme::layouts.auth')

@section('heading', 'Verify your email')

@section('content')
@if (session('status') === 'verification-link-sent')
    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
        A fresh verification link has been sent to your email address.
    </div>
@endif

<p class="mb-5 text-sm text-gray-500 text-center">
    Thanks for signing up! Before getting started, please verify your email address by clicking the link we emailed you.
    If you didn't receive it, we can send another.
</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        Resend verification email
    </button>
</form>

@if(setting('controls.email_verification', '1') !== '1')
<a href="{{ route('user.dashboard') }}"
   class="mt-4 block w-full text-center text-sm text-gray-500 hover:text-gray-700">
    {{ __('Skip for now') }}
</a>
@endif

<form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700">
        {{ __('Sign out') }}
    </button>
</form>
@endsection

@extends('theme::layouts.frontend')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 text-center">
    <h1 class="text-4xl font-bold text-gray-900">Welcome to {{ config('app.name') }}</h1>
    <p class="mt-4 text-lg text-gray-500">Your SaaS starter kit is ready.</p>
    <div class="mt-8 flex justify-center gap-4">
        <a href="{{ route('register') }}" class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-medium text-white hover:bg-brand-700">
            Get started
        </a>
        <a href="{{ route('login') }}" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Sign in
        </a>
    </div>
</div>
@endsection

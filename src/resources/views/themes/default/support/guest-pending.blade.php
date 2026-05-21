@extends('theme::layouts.frontend')
@section('title', 'Verify Your Email')
@section('content')
<div class="max-w-md mx-auto px-4 py-24 text-center">
    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <x-heroicon-o-envelope class="w-8 h-8 text-blue-600" />
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Check your inbox</h1>
    <p class="text-gray-600 mb-2">We sent a verification link to <strong>{{ $ticket->guest_email }}</strong>.</p>
    <p class="text-gray-500 text-sm">Click the link in that email to activate your ticket <strong>#{{ $ticket->reference }}</strong>.</p>
</div>
@endsection

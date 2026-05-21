@extends('theme::layouts.frontend')
@section('title', 'Ticket Verified')
@section('content')
<div class="max-w-md mx-auto px-4 py-24 text-center">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <x-heroicon-o-check class="w-8 h-8 text-green-600" />
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Email Verified</h1>
    <p class="text-gray-600 mb-4">Your ticket <strong>#{{ $ticket->reference }}</strong> is now active. Our team will be in touch shortly.</p>
    <a href="{{ route('support.guest.show', ['reference' => $ticket->reference, 'email' => $ticket->guest_email]) }}"
        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">View Ticket</a>
</div>
@endsection

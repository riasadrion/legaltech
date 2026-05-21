@extends('theme::layouts.frontend')
@section('title', 'Unsubscribe')
@section('content')
<div class="max-w-md mx-auto px-4 py-24 text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Unsubscribe</h1>
    @if($email)
    <p class="text-gray-600 mb-6">You are about to unsubscribe <strong>{{ $email }}</strong> from all emails.</p>
    <form method="POST" action="{{ route('notifications.unsubscribe.handle') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request('token') }}">
        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Confirm Unsubscribe</button>
    </form>
    <div class="mt-4"><a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-gray-600">Never mind</a></div>
    @else
    <p class="text-red-600">Invalid or expired unsubscribe link.</p>
    @endif
</div>
@endsection

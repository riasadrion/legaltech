@extends('theme::layouts.frontend')
@section('title', 'Ticket #' . $ticket->reference)
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $ticket->subject }}</h1>
    <p class="text-sm text-gray-500 mb-6">
        Ref: <span class="font-mono font-bold text-brand-600">#{{ $ticket->reference }}</span>
        · {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
    </p>

    <div class="space-y-4">
        @foreach($ticket->messages as $msg)
        <div class="rounded-xl border {{ $msg->isFromAdmin() ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-white' }} p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-900">{{ $msg->isFromAdmin() ? 'Support Team' : 'You' }}</span>
                <span class="text-xs text-gray-400">{{ $msg->created_at->format('M j, Y g:i A') }}</span>
            </div>
            <div class="text-sm text-gray-800 prose prose-sm max-w-none">
                <x-safe-html :content="$msg->body" />
            </div>
        </div>
        @endforeach
    </div>

    @if($ticket->isOpen())
    <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Reply</h3>
        <form method="POST" action="{{ route('support.guest.reply', $ticket->reference) }}">
            @csrf
            <input type="hidden" name="email" value="{{ $ticket->guest_email }}">
            <textarea name="body" rows="5" required placeholder="Type your reply…"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500">{{ old('body') }}</textarea>
            <div class="mt-4">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Send Reply</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection

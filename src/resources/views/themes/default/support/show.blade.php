@extends('theme::layouts.frontend')
@section('title', 'Ticket #' . $ticket->reference)
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('user.support.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← My Tickets</a>
    </div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Ref: <span class="font-mono font-bold text-brand-600">#{{ $ticket->reference }}</span>
                · {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                · {{ $ticket->category }}
            </p>
        </div>
        @if($ticket->isOpen())
        <form method="POST" action="{{ route('user.support.close', $ticket->reference) }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg px-3 py-1.5"
                data-confirm="Close this ticket?" data-confirm-title="Close ticket">Close Ticket</button>
        </form>
        @endif
    </div>

    <div class="space-y-4">
        @foreach($ticket->messages as $msg)
        <div class="rounded-xl border {{ $msg->isFromAdmin() ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-white' }} p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-900">
                    {{ $msg->isFromAdmin() ? 'Support Team' : 'You' }}
                </span>
                <span class="text-xs text-gray-400">{{ $msg->created_at->format('M j, Y g:i A') }}</span>
            </div>
            <div class="text-sm text-gray-800 prose prose-sm max-w-none">
                <x-safe-html :content="$msg->body" />
            </div>
            @if($msg->attachments->isNotEmpty())
            <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap gap-2">
                @foreach($msg->attachments as $att)
                <a href="{{ $att->signedUrl() }}" target="_blank"
                    class="flex items-center gap-1.5 text-xs text-brand-600 hover:text-brand-700 bg-white border border-gray-200 rounded px-2.5 py-1.5">
                    <x-heroicon-o-paper-clip class="w-3.5 h-3.5" />
                    {{ $att->original_name }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    @if($ticket->isOpen())
    <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Add Reply</h3>
        <form method="POST" action="{{ route('user.support.reply', $ticket->reference) }}" enctype="multipart/form-data">
            @csrf
            <textarea name="body" rows="5" required placeholder="Type your message…"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500 @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            <div class="mt-3">
                <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.txt,.zip"
                    class="text-sm text-gray-600">
                <p class="text-xs text-gray-400 mt-1">Up to 5 files, 10 MB each.</p>
            </div>

            <div class="mt-4">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Send Reply</button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-5 text-center text-sm text-gray-500">
        This ticket is closed.
        <a href="{{ route('user.support.create') }}" class="text-brand-600 hover:text-brand-700 ml-1">Open a new ticket →</a>
    </div>
    @endif
</div>
@endsection

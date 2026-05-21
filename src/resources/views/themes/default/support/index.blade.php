@extends('theme::layouts.frontend')
@section('title', 'My Support Tickets')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Tickets</h1>
        <a href="{{ route('user.support.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">New Ticket</a>
    </div>

    <div class="space-y-3">
        @forelse($tickets as $ticket)
        @php
            $statusColor = match($ticket->status) {
                'open'        => 'bg-green-100 text-green-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'waiting'     => 'bg-yellow-100 text-yellow-800',
                'closed'      => 'bg-gray-100 text-gray-500',
                default       => 'bg-gray-100 text-gray-500',
            };
        @endphp
        <a href="{{ route('user.support.show', $ticket->reference) }}"
            class="block bg-white rounded-xl border border-gray-200 hover:border-brand-300 p-4 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono text-xs font-bold text-brand-600">#{{ $ticket->reference }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                    <p class="font-medium text-gray-900 text-sm">{{ $ticket->subject }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->category }} · {{ $ticket->updated_at->diffForHumans() }}</p>
                </div>
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" />
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-gray-400">
            <p class="mb-4">No tickets yet.</p>
            <a href="{{ route('user.support.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Open a Ticket</a>
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
</div>
@endsection

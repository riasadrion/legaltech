@extends('theme::layouts.user')

@section('breadcrumb', __('Support'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="ud-page-title">{{ __('Support Tickets') }}</h1>
            <p class="ud-page-sub">{{ __('Get help from our support team.') }}</p>
        </div>
        <a href="{{ route('user.support.create') }}" class="ud-btn-primary inline-flex">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('New Ticket') }}
        </a>
    </div>

    <div class="ud-card">
        @if($tickets->isEmpty())
            <div class="ud-empty py-16">
                <x-heroicon-o-chat-bubble-left-right class="h-12 w-12" />
                <div class="ud-empty-title">{{ __('No tickets yet') }}</div>
                <div class="ud-empty-body">{{ __('Open a ticket and our team will get back to you.') }}</div>
                <a href="{{ route('user.support.create') }}" class="ud-btn-primary inline-flex mt-2">
                    {{ __('Open a Ticket') }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/8">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">{{ __('Reference') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">{{ __('Subject') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">{{ __('Category') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">{{ __('Updated') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($tickets as $ticket)
                        @php
                            $sc = match($ticket->status) {
                                'open'        => 'ud-badge-green',
                                'in_progress' => 'ud-badge-lime',
                                'waiting'     => 'ud-badge-amber',
                                'closed'      => 'ud-badge-gray',
                                default       => 'ud-badge-gray',
                            };
                        @endphp
                        <tr class="hover:bg-white/3 transition-colors cursor-pointer"
                            onclick="window.location='{{ route('user.support.show', $ticket->reference) }}'">
                            <td class="px-4 py-3 font-mono text-xs text-white/50">#{{ $ticket->reference }}</td>
                            <td class="px-4 py-3 font-medium text-white max-w-xs truncate">{{ $ticket->subject }}</td>
                            <td class="px-4 py-3 text-white/60 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</td>
                            <td class="px-4 py-3">
                                <span class="ud-badge {{ $sc }}">{{ __(ucfirst(str_replace('_', ' ', $ticket->status))) }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-white/40 whitespace-nowrap">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
            <div class="border-t border-white/8 px-4 py-4 text-xs text-white/45">
                {{ $tickets->links() }}
            </div>
            @endif
        @endif
    </div>

</div>
@endsection

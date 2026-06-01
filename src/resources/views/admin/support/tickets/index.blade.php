@extends('admin.layouts.app')

@section('title', __('Support Tickets'))

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Support Tickets')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Manage user support requests.')</p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <x-status-pills :items="$statusPills" :current="request('status', '')" />
            <select name="priority" onchange="this.form.submit()" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm">
                <option value="">@lang('All priority')</option>
                @foreach(\App\Domain\Support\Models\SupportTicket::priorities() as $p)
                <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst(__($p)) }}</option>
                @endforeach
            </select>
            <select name="category" onchange="this.form.submit()" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm">
                <option value="">@lang('All categories')</option>
                @foreach(\App\Domain\Support\Models\SupportTicket::categories() as $c)
                <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>{{ ucfirst(__($c)) }}</option>
                @endforeach
            </select>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" :placeholder="__('Reference or subject…')"
                   class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">@lang('Search')</button>
            @if(request()->hasAny(['search', 'status', 'priority', 'category']))
                <a href="{{ route('admin.support.tickets.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Ref')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Subject')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('From')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Priority')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Assigned')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Updated')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $ticket)
                @php
                    $statusColor = match($ticket->status) {
                        'open'        => 'bg-green-100 text-green-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'waiting'     => 'bg-yellow-100 text-yellow-700',
                        'closed'      => 'bg-gray-100 text-gray-500',
                        default       => 'bg-gray-100 text-gray-500',
                    };
                    $priorityColor = match($ticket->priority) {
                        'urgent' => 'text-red-600 font-bold',
                        'high'   => 'text-orange-600 font-semibold',
                        default  => 'text-gray-600',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" :data-label="__('Ref')">
                        <a href="{{ route('admin.support.tickets.show', $ticket) }}" class="font-mono text-xs font-semibold text-brand-600 hover:text-brand-700">#{{ $ticket->reference }}</a>
                    </td>
                    <td class="px-4 py-3 max-w-xs" :data-label="__('Subject')">
                        <div class="truncate text-sm font-medium text-gray-900">{{ $ticket->subject }}</div>
                        <div class="text-xs text-gray-400">{{ ucfirst(__($ticket->category)) }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600" :data-label="__('From')">{{ $ticket->getAuthorName() }}</td>
                    <td class="px-4 py-3 text-xs {{ $priorityColor }}" :data-label="__('Priority')">{{ ucfirst(__($ticket->priority)) }}</td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', __($ticket->status))) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600" :data-label="__('Assigned')">{{ $ticket->assignee?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400" :data-label="__('Updated')">{{ $ticket->updated_at->diffForHumans() }}</td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.support.tickets.show', $ticket) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                            @lang('View')
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No support tickets found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tickets->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $tickets->firstItem(), 'last' => $tickets->lastItem(), 'total' => $tickets->total()]) }}</span>
            <x-pagination :paginator="$tickets" />
        </div>
        @endif
    </div>
</div>
@endsection

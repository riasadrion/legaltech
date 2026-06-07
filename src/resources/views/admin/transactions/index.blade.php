@extends('admin.layouts.app')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/global/css/flatpickr.min.css') }}" id="flatpickr-css">
@endpush

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Transaction Logs')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Full ledger of all wallet credits, debits and adjustments.')</p>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <x-heroicon-o-arrow-down-circle class="w-5 h-5"/>
            </div>
            <div>
                <div class="text-xs text-gray-500">@lang('Total Credits')</div>
                <div class="text-lg font-bold text-gray-900">${{ number_format($summary['total_credit'], 2) }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500">
                <x-heroicon-o-arrow-up-circle class="w-5 h-5"/>
            </div>
            <div>
                <div class="text-xs text-gray-500">@lang('Total Debits')</div>
                <div class="text-lg font-bold text-gray-900">${{ number_format($summary['total_debit'], 2) }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <x-heroicon-o-list-bullet class="w-5 h-5"/>
            </div>
            <div>
                <div class="text-xs text-gray-500">@lang('Total Entries')</div>
                <div class="text-lg font-bold text-gray-900">{{ number_format($summary['count']) }}</div>
            </div>
        </div>
    </div>


    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('User name or email…') }}"
               class="h-9 w-56 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"/>

        <select name="type" data-auto-submit
                class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">@lang('All types')</option>
            <option value="credit"       {{ request('type') === 'credit'       ? 'selected' : '' }}>@lang('Credit')</option>
            <option value="debit"        {{ request('type') === 'debit'        ? 'selected' : '' }}>@lang('Debit')</option>
            <option value="adjustment"   {{ request('type') === 'adjustment'   ? 'selected' : '' }}>@lang('Adjustment')</option>
            <option value="fee"          {{ request('type') === 'fee'          ? 'selected' : '' }}>@lang('Fee')</option>
            <option value="refund"       {{ request('type') === 'refund'       ? 'selected' : '' }}>@lang('Refund')</option>
            <option value="transfer_in"  {{ request('type') === 'transfer_in'  ? 'selected' : '' }}>@lang('Transfer In')</option>
            <option value="transfer_out" {{ request('type') === 'transfer_out' ? 'selected' : '' }}>@lang('Transfer Out')</option>
        </select>

        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" fill="none"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </span>
            <input type="text" id="filter-daterange" name="daterange"
                   value="{{ request('from') && request('to') ? request('from').' to '.request('to') : '' }}"
                   placeholder="Date range" readonly
                   class="h-9 w-52 rounded-lg border border-gray-300 pl-9 pr-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 cursor-pointer"/>
        </div>
        <input type="hidden" name="from" id="filter-from" value="{{ request('from') }}">
        <input type="hidden" name="to" id="filter-to" value="{{ request('to') }}">

        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
            @lang('Filter')
        </button>

        @if(request()->hasAny(['q','type','from','to','daterange']))
        <a href="{{ route('admin.transactions.index') }}"
           class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">
            @lang('Clear')
        </a>
        @endif
    </form>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Type')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Amount')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Balance After')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Description')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                @php
                    $typeColors = [
                        'credit'       => 'bg-emerald-100 text-emerald-700',
                        'debit'        => 'bg-red-100 text-red-600',
                        'adjustment'   => 'bg-blue-100 text-blue-700',
                        'fee'          => 'bg-orange-100 text-orange-700',
                        'refund'       => 'bg-teal-100 text-teal-700',
                        'transfer_in'  => 'bg-indigo-100 text-indigo-700',
                        'transfer_out' => 'bg-purple-100 text-purple-700',
                    ];
                    $tc = $typeColors[$tx->type] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-400" :data-label="__('ID')">#{{ $tx->id }}</td>
                    <td class="px-4 py-3" :data-label="__('User')">
                        @if($tx->user)
                        <a href="{{ route('admin.users.show', $tx->user) }}" class="hover:text-brand-600">
                            <div class="font-medium text-gray-900">{{ $tx->user->name ?: '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $tx->user->email }}</div>
                        </a>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3" :data-label="__('Type')">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $tc }}">
                            {{ ucfirst(str_replace('_', ' ', $tx->type)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold {{ $tx->isCredit() ? 'text-emerald-600' : 'text-red-600' }}" :data-label="__('Amount')">
                        {{ $tx->formattedAmount() }}
                    </td>
                    <td class="hide-mobile px-4 py-3 text-xs text-gray-600 font-mono" :data-label="__('Balance After')">
                        ${{ number_format($tx->balance_after, 2) }}
                    </td>
                    <td class="hide-mobile px-4 py-3 text-xs text-gray-500 max-w-xs truncate" :data-label="__('Description')" title="{{ $tx->description }}">
                        {{ $tx->description ?: '—' }}
                    </td>
                    <td class="hide-mobile px-4 py-3 text-xs text-gray-400 whitespace-nowrap" :data-label="__('Date')">
                        {{ formatDate($tx->created_at) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No transactions found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($transactions->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $transactions->firstItem(), 'last' => $transactions->lastItem(), 'total' => $transactions->total()]) }}</span>
            <x-admin::pagination :show-total="false" :paginator="$transactions" />
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="{{ asset('assets/static/global/js/flatpickr.min.js') }}"></script>
<script>
onAdminReady(function () {
    'use strict';

    if (document.documentElement.getAttribute('data-theme') === 'dark') {
        $('#flatpickr-css').attr('href', '{{ asset('assets/static/global/css/flatpickr.dark.css') }}');
    }

    flatpickr('#filter-daterange', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        allowInput: false,
        onChange: function (selectedDates) {
            $('#filter-from').val(selectedDates[0] ? flatpickr.formatDate(selectedDates[0], 'Y-m-d') : '');
            $('#filter-to').val(selectedDates[1] ? flatpickr.formatDate(selectedDates[1], 'Y-m-d') : '');
        }
    });

    $('[data-auto-submit]').on('change', function () { $(this).closest('form').trigger('submit'); });
});
</script>
@endpush
@endsection

@extends('admin.layouts.app')

@section('title', __('Reports'))

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/flatpickr.min.css') }}" id="flatpickr-css">
@endpush

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Reports')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Filter and export platform data.')</p>
    </div>


    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-2">

        <select name="type" data-auto-submit
                class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            @foreach(['transactions' => __('Transactions'), 'payments' => __('Payments'), 'logins' => __('Login History'), 'notifications' => __('Notifications')] as $val => $lbl)
            <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
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

        <a href="{{ route('admin.reports.export', request()->all()) }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            <x-heroicon-o-arrow-down-tray class="h-4 w-4"/>
            @lang('Export CSV')
        </a>

        @if(request()->hasAny(['from','to','daterange']))
        <a href="{{ route('admin.reports.index', ['type' => $type]) }}"
           class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">
            @lang('Clear')
        </a>
        @endif
    </form>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @if($data->isEmpty())
        <div class="px-4 py-14 text-center text-sm text-gray-400">@lang('No records found. Try adjusting the filters.')</div>
        @else
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    @if($type === 'transactions')
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Type')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Amount')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Balance After')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Description')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                    @elseif($type === 'payments')
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Gateway')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Amount')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                    @elseif($type === 'logins')
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('IP Address')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User Agent')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                    @elseif($type === 'notifications')
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Template')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Channel')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data as $row)
                @php $row = (array) $row; @endphp
                <tr class="hover:bg-gray-50">
                    @if($type === 'transactions')
                        @php
                            $typeColors = ['credit'=>'bg-emerald-100 text-emerald-700','debit'=>'bg-red-100 text-red-600','adjustment'=>'bg-blue-100 text-blue-700','fee'=>'bg-orange-100 text-orange-700','refund'=>'bg-teal-100 text-teal-700','transfer_in'=>'bg-indigo-100 text-indigo-700','transfer_out'=>'bg-purple-100 text-purple-700'];
                            $tc = $typeColors[$row['type']] ?? 'bg-gray-100 text-gray-600';
                            $isCredit = in_array($row['type'], ['credit','transfer_in','refund']);
                        @endphp
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">#{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['user_email'] ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $tc }}">{{ ucfirst(str_replace('_', ' ', $row['type'])) }}</span></td>
                        <td class="px-4 py-3 font-semibold text-sm {{ $isCredit ? 'text-emerald-600' : 'text-red-600' }}">{{ $isCredit ? '+' : '-' }}${{ number_format(abs($row['amount']), 2) }}</td>
                        <td class="hide-mobile px-4 py-3 text-xs font-mono text-gray-600">${{ number_format($row['balance_after'], 2) }}</td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $row['description'] ?? '—' }}</td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $row['created_at'] }}</td>

                    @elseif($type === 'payments')
                        @php $sc = match($row['status']) { 'completed'=>'bg-green-100 text-green-700','awaiting_approval'=>'bg-amber-100 text-amber-700','failed'=>'bg-red-100 text-red-600', default=>'bg-gray-100 text-gray-500' }; @endphp
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">#{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['user_email'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['gateway'] ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-sm text-gray-900">${{ number_format($row['amount'], 2) }}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</span></td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $row['created_at'] }}</td>

                    @elseif($type === 'logins')
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">#{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['user_email'] ?? '—' }}</td>
                        <td class="hide-mobile px-4 py-3 text-xs font-mono text-gray-600">{{ $row['ip_address'] ?? '—' }}</td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $row['user_agent'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $row['created_at'] }}</td>

                    @elseif($type === 'notifications')
                        @php $sc = match($row['status'] ?? '') { 'sent'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-600', default=>'bg-gray-100 text-gray-500' }; @endphp
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">#{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $row['template_key'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($row['channel'] ?? '—') }}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst($row['status'] ?? '—') }}</span></td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $row['created_at'] }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $data->firstItem(), 'last' => $data->lastItem(), 'total' => $data->total()]) }}</span>
            <x-pagination :paginator="$data" />
        </div>
        @endif
        @endif
    </div>

</div>

@push('scripts')
<script src="{{ asset('assets/static/js/flatpickr.min.js') }}"></script>
<script>
onAdminReady(function () {
    'use strict';

    if (document.documentElement.getAttribute('data-theme') === 'dark') {
        $('#flatpickr-css').attr('href', '{{ asset('assets/static/css/flatpickr.dark.css') }}');
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

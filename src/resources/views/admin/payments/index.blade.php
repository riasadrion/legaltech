@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Payments') }}</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ __('All user payment requests.') }}</p>
    </div>


    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-status-pills :items="$statusPills" :current="request('status', '')" />
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Name or email…') }}"
                   class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">{{ __('Filter') }}</button>
            @if(request()->hasAny(['q','gateway']))
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('User') }}</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Gateway') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" data-label="{{ __('User') }}">
                        <div class="font-medium text-gray-900">{{ $payment->user->name ?: '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $payment->user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700 capitalize" data-label="{{ __('Gateway') }}">{{ $payment->gateway->name }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900" data-label="{{ __('Amount') }}">{{ $payment->formattedAmount() }}</td>
                    <td class="px-4 py-3" data-label="{{ __('Status') }}">
                        @php
                            $sc = match($payment->status) {
                                'completed'         => 'bg-green-100 text-green-700',
                                'awaiting_approval' => 'bg-amber-100 text-amber-700',
                                'failed'            => 'bg-red-100 text-red-700',
                                'cancelled'         => 'bg-gray-100 text-gray-500',
                                default             => 'bg-blue-100 text-blue-700',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst(str_replace('_', ' ', __($payment->status))) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" data-label="{{ __('Date') }}">{{ formatDate($payment->created_at) }}</td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.payments.show', $payment) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                            <x-heroicon-o-eye class="h-3.5 w-3.5" />
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No payments found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($payments->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $payments->firstItem(), 'last' => $payments->lastItem(), 'total' => $payments->total()]) }}</span>
            <x-pagination :paginator="$payments" />
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
onAdminReady(function () {
    'use strict';
    $('[data-auto-submit]').on('change', function () { $(this).closest('form').trigger('submit'); });
});
</script>
@endpush
@endsection

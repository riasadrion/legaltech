@extends('admin.layouts.app')

@section('title', __('Payouts'))

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Payouts') }}</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ __('Review and approve user payout requests.') }}</p>
    </div>

    <x-status-pills :items="$statusPills" :current="$status" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('User') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Method') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Amount') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Fee') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payouts as $w)
                @php $sc = match($w->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-gray-100 text-gray-500', default => 'bg-amber-100 text-amber-700' }; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" data-label="{{ __('User') }}">
                        <div class="font-medium text-gray-900">{{ $w->user->name }}</div>
                        <div class="text-xs text-gray-400">{{ $w->user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700" data-label="{{ __('Method') }}">{{ $w->method->getLocaleName() }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900" data-label="{{ __('Amount') }}">{{ $w->formattedAmount() }}</td>
                    <td class="px-4 py-3 text-gray-500" data-label="{{ __('Fee') }}">{{ $w->formattedFee() }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" data-label="{{ __('Date') }}">{{ formatDate($w->created_at) }}</td>
                    <td class="px-4 py-3" data-label="{{ __('Status') }}">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst(__($w->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.payouts.show', $w) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                            <x-heroicon-o-eye class="h-3.5 w-3.5" />
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No payouts found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($payouts->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $payouts->firstItem(), 'last' => $payouts->lastItem(), 'total' => $payouts->total()]) }}</span>
            <x-pagination :paginator="$payouts" />
        </div>
        @endif
    </div>
</div>
@endsection

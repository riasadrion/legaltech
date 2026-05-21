@extends('admin.layouts.app')

@section('title', 'Payout Methods')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Payout Methods</h1>
            <p class="mt-0.5 text-sm text-gray-500">Configure payout methods available to users.</p>
        </div>
        @if(auth('admin')->user()->can('payments.manage'))
        <a href="{{ route('admin.payout-methods.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            New method
        </a>
        @endif
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Method</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Limits</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Fee</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">KYC</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($methods as $method)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" data-label="{{ __('Method') }}">
                        <div class="font-semibold text-gray-900">{{ $method->getLocaleName() }}</div>
                        @if($method->getLocaleDelayText())
                            <div class="text-xs text-gray-400">{{ $method->getLocaleDelayText() }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600" data-label="{{ __('Limits') }}">
                        {{ formatAmount($method->min_amount) }} – {{ formatAmount($method->max_amount) }}
                    </td>
                    <td class="px-4 py-3 text-gray-600" data-label="{{ __('Fee') }}">
                        @if($method->fee_flat > 0 || $method->fee_percent > 0)
                            @if($method->fee_flat > 0) {{ formatAmount($method->fee_flat) }} @endif
                            @if($method->fee_flat > 0 && $method->fee_percent > 0) + @endif
                            @if($method->fee_percent > 0) {{ $method->fee_percent }}% @endif
                        @else
                            <span class="text-gray-400">None</span>
                        @endif
                    </td>
                    <td class="px-4 py-3" data-label="{{ __('KYC') }}">
                        @if($method->kyc_required)
                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Required</span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">Optional</span>
                        @endif
                    </td>
                    <td class="px-4 py-3" data-label="{{ __('Status') }}">
                        @if($method->enabled)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>Disabled
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        @if(auth('admin')->user()->can('payments.manage'))
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('admin.payout-methods.toggle', $method) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                                    {{ $method->enabled ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.payout-methods.edit', $method) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                                <x-heroicon-o-pencil class="h-3.5 w-3.5" />
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.payout-methods.destroy', $method) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" data-confirm="Delete this payout method?"
                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50">
                                    <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                    Delete
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400">No payout methods found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

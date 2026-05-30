@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Plans') }}</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ __('Manage subscription plans and pricing.') }}</p>
    </div>


    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <x-status-pills :items="$statusPills" :current="request('status', '')" />
            <a href="{{ route('admin.plans.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-plus class="h-4 w-4" />
                {{ __('New Plan') }}
            </a>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Plan name…') }}"
                   class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">{{ __('Search') }}</button>
            @if(request()->hasAny(['q']))
                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Price') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Cycle') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Credits') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Trial') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($plans as $plan)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" data-label="{{ __('Name') }}">{{ $plan->name }}</td>
                    <td class="px-4 py-3 text-gray-600" data-label="{{ __('Price') }}">{{ $plan->formattedPrice() }}</td>
                    <td class="px-4 py-3 text-gray-600" data-label="{{ __('Cycle') }}">{{ ucfirst($plan->billing_cycle) }}</td>
                    <td class="px-4 py-3 text-gray-600" data-label="{{ __('Credits') }}">{{ $plan->credits ?: '—' }}</td>
                    <td class="px-4 py-3" data-label="{{ __('Status') }}">
                        @php
                            $sc = $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ $plan->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.plans.edit', $plan) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 hover:text-gray-900 me-2">
                            {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 hover:text-gray-900 me-2">
                                {{ $plan->is_active ? __('Disable') : __('Enable') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" data-confirm="{{ __('Delete this plan?') }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No plans found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($plans->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $plans->firstItem(), 'last' => $plans->lastItem(), 'total' => $plans->total()]) }}</span>
            <x-pagination :paginator="$plans" />
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

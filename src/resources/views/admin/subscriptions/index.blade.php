@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Subscriptions')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('All user subscriptions across plans.')</p>
    </div>


    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-status-pills :items="$statusPills" :current="request('status', '')" />
        <form method="GET" class="flex flex-wrap items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <select name="plan" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value="">@lang('All plans')</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(request('plan') == $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ request('q') }}" :placeholder="__('Name or email…')"
                   class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">@lang('Filter')</button>
            @if(request()->hasAny(['q','plan']))
                <a href="{{ route('admin.subscriptions.index', request('status') ? ['status' => request('status')] : []) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Plan')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Credits')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Period Ends')</th>
                    <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Started')</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" :data-label="__('User')">
                        <div class="font-medium text-gray-900">{{ $sub->user->name ?: '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $sub->user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700" :data-label="__('Plan')">
                        <div class="font-medium">{{ $sub->plan->name ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ ucfirst($sub->plan->billing_cycle ?? '') }}</div>
                    </td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        @php
                            $sc = match($sub->status) {
                                'active'    => 'bg-green-100 text-green-700',
                                'past_due'  => 'bg-amber-100 text-amber-700',
                                'cancelled' => 'bg-gray-100 text-gray-500',
                                'expired'   => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst(str_replace('_', ' ', __($sub->status))) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700" :data-label="__('Credits')">{{ number_format($sub->credits_remaining) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" :data-label="__('Period Ends')">
                        {{ formatDate($sub->current_period_end, false) ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" :data-label="__('Started')">
                        {{ formatDate($sub->created_at, false) }}
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.subscriptions.show', $sub) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-300 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                            <x-heroicon-o-eye class="h-3.5 w-3.5" />
                            @lang('View')
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No subscriptions found.')</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($subscriptions->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $subscriptions->firstItem(), 'last' => $subscriptions->lastItem(), 'total' => $subscriptions->total()]) }}</span>
            <x-pagination :show-total="false" :paginator="$subscriptions" />
        </div>
        @endif
    </div>
</div>
@endsection

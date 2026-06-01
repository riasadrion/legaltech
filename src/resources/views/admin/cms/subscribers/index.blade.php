@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Subscribers')</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ __(':total total', ['total' => $subscribers->total()]) }}</p>
        </div>
        <a href="{{ route('admin.cms.subscribers.broadcast') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-paper-airplane class="h-4 w-4" />
            @lang('Broadcast newsletter')
        </a>
    </div>


    @php
        $statusPills = [
            ['value' => '', 'label' => __('All'), 'count' => $subscribers->total()],
            ['value' => 'pending', 'label' => __('Pending'), 'count' => $subscribers->where('status', 'pending')->count()],
            ['value' => 'confirmed', 'label' => __('Confirmed'), 'count' => $subscribers->where('status', 'confirmed')->count()],
            ['value' => 'unsubscribed', 'label' => __('Unsubscribed'), 'count' => $subscribers->where('status', 'unsubscribed')->count()],
        ];
    @endphp
    <x-status-pills :items="$statusPills" :current="request('status', '')" param="status" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Email')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Name')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Confirmed')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscribers as $subscriber)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-900" :data-label="__('Email')">{{ $subscriber->email }}</td>
                    <td class="px-4 py-3 text-gray-500" :data-label="__('Name')">{{ $subscriber->name ?? '—' }}</td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        @if($subscriber->status === 'confirmed')
                            <span class="inline-flex items-center rounded-full border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">@lang('Confirmed')</span>
                        @elseif($subscriber->status === 'pending')
                            <span class="inline-flex items-center rounded-full border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400">@lang('Pending')</span>
                        @else
                            <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:text-slate-300">@lang('Unsubscribed')</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500" :data-label="__('Confirmed')">
                        {{ $subscriber->confirmed_at?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <form method="POST" action="{{ route('admin.cms.subscribers.destroy', $subscriber) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" :data-confirm="__('Remove this subscriber?')"
                                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50">
                                <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                @lang('Remove')
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No subscribers found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($subscribers->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $subscribers->firstItem(), 'last' => $subscribers->lastItem(), 'total' => $subscribers->total()]) }}</span>
            <x-pagination :paginator="$subscribers" />
        </div>
        @endif
    </div>
</div>
@endsection
@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Activity Log')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Admin action history.')</p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">@lang('Action')</label>
                <select name="action" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">@lang('All actions')</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">@lang('Admin')</label>
                <input type="text" name="causer" value="{{ request('causer') }}" :placeholder="__('Filter by email…')"
                       class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                <x-heroicon-o-funnel class="h-4 w-4" />
                @lang('Filter')
            </button>
            @if(request()->hasAny(['action','causer']))
                <a href="{{ route('admin.activity-log.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('When')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Admin')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Action')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Subject')</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" :data-label="__('When')">{{ formatDate($log->created_at) }}</td>
                    <td class="px-4 py-3 text-gray-700" :data-label="__('Admin')">{{ $log->causer?->email ?? '—' }}</td>
                    <td class="px-4 py-3" :data-label="__('Action')">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500" :data-label="__('Subject')">
                        {{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.activity-log.show', $log) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                            <x-heroicon-o-eye class="h-3.5 w-3.5" />
                            @lang('View')
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No activity found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $logs->firstItem(), 'last' => $logs->lastItem(), 'total' => $logs->total()]) }}</span>
            <x-pagination :paginator="$logs" />
        </div>
        @endif
    </div>
</div>
@endsection

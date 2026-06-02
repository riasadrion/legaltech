@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Activity Log')</h1>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-slate-400">@lang('Admin action history.')</p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-slate-400">@lang('Action')</label>
                <select name="action" class="h-9 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 text-sm text-gray-900 dark:text-slate-200 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">@lang('All actions')</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-slate-400">@lang('Admin')</label>
                <input type="text" name="causer" value="{{ request('causer') }}" placeholder="{{ __('Filter by email…') }}"
                       class="h-9 w-52 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 text-sm text-gray-900 dark:text-slate-200 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                <x-heroicon-o-funnel class="h-4 w-4" />
                @lang('Filter')
            </button>
            @if(request()->hasAny(['action','causer']))
                <a href="{{ route('admin.activity-log.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 dark:border-slate-600 px-3 py-1.5 text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-800/60">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">@lang('When')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">@lang('Admin')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">@lang('Action')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">@lang('Subject')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">@lang('Detail')</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($logs as $log)
                @php
                    $actionColor = match($log->action) {
                        'created' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                        'updated' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                        'deleted' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                        default   => 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300',
                    };
                    $changedFields = $log->properties['old'] ?? null
                        ? array_keys($log->properties['old'])
                        : ($log->properties['changed'] ?? []);
                    $subjectLabel = $log->subject
                        ? ($log->subject->name ?? $log->subject->email ?? $log->subject->title ?? null)
                        : null;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 whitespace-nowrap">{{ formatDate($log->created_at) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-slate-300">{{ $log->causer?->email ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $actionColor }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                        @if($subjectLabel)
                            <div class="text-xs text-gray-500 dark:text-slate-400 truncate max-w-[180px]">{{ $subjectLabel }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400">
                        @if(!empty($changedFields))
                            {{ implode(', ', $changedFields) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.activity-log.show', $log) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-300 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600">
                            <x-heroicon-o-eye class="h-3.5 w-3.5" />
                            @lang('View')
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400 dark:text-slate-500">@lang('No activity found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/40 px-5 py-3 text-xs text-gray-500 dark:text-slate-400">
            <span>{{ __('Showing :first–:last of :total', ['first' => $logs->firstItem(), 'last' => $logs->lastItem(), 'total' => $logs->total()]) }}</span>
            <x-pagination :paginator="$logs" />
        </div>
        @endif
    </div>
</div>
@endsection

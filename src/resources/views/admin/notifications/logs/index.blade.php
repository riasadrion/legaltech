@extends('admin.layouts.app')

@section('title', __('Notification Logs'))

@section('content')
<div class="space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('admin.notifications.templates.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="page-title">@lang('Notification Logs')</h1>
            <p class="page-subtitle">@lang('History of sent notifications')</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">@lang('Channel')</label>
                <select name="channel" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">@lang('All')</option>
                    @foreach($channels as $ch)
                    <option value="{{ $ch }}" {{ request('channel') === $ch ? 'selected' : '' }}>{{ strtoupper($ch) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">@lang('Status')</label>
                <select name="status" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">@lang('All')</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>@lang('Sent')</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>@lang('Failed')</option>
                    <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>@lang('Skipped')</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">@lang('Template')</label>
                <select name="key" class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">@lang('All')</option>
                    @foreach($keys as $k)
                    <option value="{{ $k }}" {{ request('key') === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                <x-heroicon-o-funnel class="h-4 w-4" />
                @lang('Filter')
            </button>
            @if(request()->hasAny(['channel','status','key']))
                <a href="{{ route('admin.notifications.logs.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Template')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Channel')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Recipient')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Sent')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" :data-label="__('Template')">
                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-mono text-gray-700">
                            {{ $log->notification_key }}
                        </span>
                    </td>
                    <td class="px-4 py-3" :data-label="__('Channel')">
                        <span class="badge badge-neutral uppercase">{{ $log->channel }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700" :data-label="__('Recipient')">{{ $log->recipient }}</td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        <span class="badge {{ $log->status === 'sent' ? 'badge-success' : ($log->status === 'failed' ? 'badge-danger' : 'badge-neutral') }}">
                            {{ __(ucfirst($log->status)) }}
                        </span>
                        @if($log->error && $log->status !== 'skipped')
                        <div class="text-xs text-red-500 mt-0.5 truncate max-w-xs" title="{{ $log->error }}">{{ $log->error }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap" :data-label="__('Sent')">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No notification logs found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $logs->firstItem(), 'last' => $logs->lastItem(), 'total' => $logs->total()]) }}</span>
            <x-admin::pagination :show-total="false" :paginator="$logs" />
        </div>
        @endif
    </div>
</div>
@endsection
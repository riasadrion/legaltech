@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.activity-log.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Activity') #{{ $log->id }}</h1>
            <p class="text-sm text-gray-400 dark:text-slate-500">{{ formatDate($log->created_at) }}</p>
        </div>
        @php
            $actionColor = match($log->action) {
                'created' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'updated' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                'deleted' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                default   => 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300',
            };
        @endphp
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $actionColor }}">
            {{ ucfirst($log->action) }}
        </span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        {{-- Event --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Event')</h2>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">@lang('Action')</dt>
                    <dd><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $actionColor }}">{{ ucfirst($log->action) }}</span></dd>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">@lang('When')</dt>
                    <dd class="text-gray-700 dark:text-slate-300">{{ formatDate($log->created_at) }}</dd>
                </div>
                @if($log->ip_address)
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">@lang('IP address')</dt>
                    <dd class="font-mono text-xs text-gray-700 dark:text-slate-300">{{ $log->ip_address }}</dd>
                </div>
                @endif
                @if($log->user_agent)
                <div class="px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400 mb-1">@lang('User agent')</dt>
                    <dd class="text-xs text-gray-600 dark:text-slate-400 break-all">{{ $log->user_agent }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Performed by / Subject --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Performed by')</h2>
            </div>
            <div class="px-5 py-4 space-y-0.5">
                @if($log->causer)
                    <div class="font-medium text-gray-900 dark:text-white">{{ $log->causer->name ?? '—' }}</div>
                    <div class="text-xs text-gray-500 dark:text-slate-400">{{ $log->causer->email ?? '—' }}</div>
                    <div class="text-xs text-gray-400 dark:text-slate-500 pt-0.5">{{ class_basename($log->causer_type) }} #{{ $log->causer_id }}</div>
                @else
                    <div class="text-sm text-gray-400 dark:text-slate-500">@lang('System')</div>
                @endif
            </div>

            @if($log->subject_type)
            <div class="border-t border-gray-100 dark:border-slate-700 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Subject')</h2>
            </div>
            <div class="px-5 py-4 space-y-0.5">
                <div class="font-medium text-gray-900 dark:text-white">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                @if($log->subject)
                    @php $label = $log->subject->name ?? $log->subject->email ?? $log->subject->title ?? null; @endphp
                    @if($label)
                        <div class="text-xs text-gray-500 dark:text-slate-400">{{ $label }}</div>
                    @endif
                @else
                    <div class="text-xs text-gray-400 dark:text-slate-500">@lang('Subject no longer exists.')</div>
                @endif
            </div>
            @endif
        </div>

    </div>

    {{-- Changes --}}
    @if(!empty($log->properties))
    @php
        $props = $log->properties;
        $old  = $props['old'] ?? null;
        $new  = $props['attributes'] ?? null;
        $isBeforeAfter = $old !== null || $new !== null;
        $allKeys = array_unique(array_merge(array_keys($old ?? []), array_keys($new ?? [])));
    @endphp
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-x-auto">
        <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Changes')</h2>
        </div>

        @if($isBeforeAfter && !empty($allKeys))
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400 w-1/4">@lang('Field')</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400 w-[37.5%]">@lang('Before')</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400 w-[37.5%]">@lang('After')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($allKeys as $key)
                    <tr>
                        <td class="px-5 py-2.5 font-mono text-xs text-gray-500 dark:text-slate-400 align-top">{{ $key }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-600 dark:text-slate-400 break-all align-top">
                            {{ isset($old[$key]) ? (is_scalar($old[$key]) ? $old[$key] : json_encode($old[$key])) : '—' }}
                        </td>
                        <td class="px-5 py-2.5 text-xs font-medium text-gray-900 dark:text-white break-all align-top">
                            {{ isset($new[$key]) ? (is_scalar($new[$key]) ? $new[$key] : json_encode($new[$key])) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-5 py-4">
                <pre class="overflow-x-auto rounded-lg bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 p-4 text-xs text-gray-700 dark:text-slate-300">{{ json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif
    </div>
    @endif

</div>
@endsection

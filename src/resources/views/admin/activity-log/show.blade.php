@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.activity-log.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Activity') #{{ $log->id }}</h1>
            <p class="text-sm text-gray-400">{{ formatDate($log->created_at) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('Event')</h3>
            </div>
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">@lang('Action')</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                            {{ $log->action }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">@lang('When')</dt>
                    <dd class="text-gray-700">{{ formatDate($log->created_at) }}</dd>
                </div>
                @if($log->ip_address)
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">@lang('IP address')</dt>
                    <dd class="font-mono text-xs text-gray-700">{{ $log->ip_address }}</dd>
                </div>
                @endif
                @if($log->user_agent)
                <div class="px-5 py-3">
                    <dt class="text-xs text-gray-500 mb-1">@lang('User agent')</dt>
                    <dd class="text-xs text-gray-600 break-all">{{ $log->user_agent }}</dd>
                </div>
                @endif
            </dl>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('Performed by')</h3>
            </div>
            <div class="px-5 py-4 space-y-1">
                @if($log->causer)
                    <div class="font-medium text-gray-900">{{ $log->causer->name ?? '—' }}</div>
                    <div class="text-xs text-gray-500">{{ $log->causer->email ?? '—' }}</div>
                    <div class="mt-1 text-xs text-gray-400">{{ class_basename($log->causer_type) }} #{{ $log->causer_id }}</div>
                @else
                    <div class="text-sm text-gray-400">@lang('System')</div>
                @endif
            </div>

            @if($log->subject_type)
            <div class="border-t border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('Subject')</h3>
            </div>
            <div class="px-5 py-4 space-y-1">
                <div class="text-sm text-gray-700">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                @if($log->subject)
                    @php
                        $label = $log->subject->name ?? $log->subject->email ?? $log->subject->title ?? null;
                    @endphp
                    @if($label)
                        <div class="text-xs text-gray-500">{{ $label }}</div>
                    @endif
                @else
                    <div class="text-xs text-gray-400">@lang('Subject no longer exists.')</div>
                @endif
            </div>
            @endif
        </div>
    </div>


    @if(!empty($log->properties))
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h3 class="text-sm font-semibold text-gray-900">@lang('Properties')</h3>
            <p class="mt-0.5 text-xs text-gray-500">@lang('Captured context for this event.')</p>
        </div>
        @php
            $props = $log->properties;
            $hasChanges = isset($props['old']) || isset($props['attributes']);
        @endphp

        @if($hasChanges)
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                <div class="px-5 py-4">
                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Before')</h4>
                    @if(!empty($props['old']))
                        <dl class="space-y-1.5 text-sm">
                            @foreach($props['old'] as $key => $value)
                                <div class="flex flex-col">
                                    <dt class="text-xs text-gray-500">{{ $key }}</dt>
                                    <dd class="text-gray-800 break-all">{{ is_scalar($value) ? $value : json_encode($value) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <div class="text-xs text-gray-400">—</div>
                    @endif
                </div>
                <div class="px-5 py-4 bg-emerald-50/30">
                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('After')</h4>
                    @if(!empty($props['attributes']))
                        <dl class="space-y-1.5 text-sm">
                            @foreach($props['attributes'] as $key => $value)
                                <div class="flex flex-col">
                                    <dt class="text-xs text-gray-500">{{ $key }}</dt>
                                    <dd class="text-gray-800 break-all">{{ is_scalar($value) ? $value : json_encode($value) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <div class="text-xs text-gray-400">—</div>
                    @endif
                </div>
            </div>
        @else
            <div class="px-5 py-4">
                <pre class="overflow-x-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-700">{{ json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection

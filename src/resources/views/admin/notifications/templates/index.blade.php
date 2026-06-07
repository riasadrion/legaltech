@extends('admin.layouts.app')

@section('title', __('Notification Templates'))

@section('content')
<x-admin::page-header :title="__('Notification Templates')">
    <x-slot name="actions">
        <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-cog-6-tooth class="h-4 w-4" />@lang('Settings')
        </a>
        <a href="{{ route('admin.notifications.logs.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-list-bullet class="h-4 w-4" />@lang('Logs')
        </a>
        <a href="{{ route('admin.notifications.health.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-heart class="h-4 w-4" />@lang('Email Health')
        </a>
    </x-slot>
</x-admin::page-header>

<x-table>
    <x-slot name="head">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Template')</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Key')</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Channels')</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
        </tr>
    </x-slot>
    @forelse($templates as $template)
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-3">
            <div class="font-medium text-gray-900">{{ $template->name }}</div>
            @if($template->description)
            <div class="text-xs text-gray-400 mt-0.5">{{ $template->description }}</div>
            @endif
        </td>
        <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $template->key }}</code></td>
        <td class="px-4 py-3">
            @foreach($template->channels ?? [] as $ch)
            @php
                $badgeClass = match($ch) {
                    'email'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                    'sms'    => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                    default  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                };
            @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}">
                {{ strtoupper($ch) }}
            </span>
            @endforeach
        </td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                {{ $template->is_active ? __('Active') : __('Inactive') }}
            </span>
        </td>
        <td class="px-4 py-3 text-right">
            <a href="{{ route('admin.notifications.templates.edit', $template) }}" class="text-sm text-brand-600 hover:text-brand-700">@lang('Edit')</a>
        </td>
    </tr>
    @empty
    <tr><td colspan="5"><x-admin::empty-state :title="__('No templates yet')" /></td></tr>
    @endforelse
</x-admin::table>
@endsection

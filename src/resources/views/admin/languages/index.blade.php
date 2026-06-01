@extends('admin.layouts.app')

@section('title', __('Languages'))

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="page-title">@lang('Languages')</h1>
            <p class="page-subtitle">@lang('Manage site languages and translations')</p>
        </div>
        <a href="{{ route('admin.languages.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('Add Language')
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Language')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Code')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Direction')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Default')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($languages as $language)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" data-label="@lang('Language')">
                        {{ $language->name }}
                        @if($language->native_name !== $language->name)
                            <span class="ml-1 text-xs text-gray-400">{{ $language->native_name }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600" data-label="@lang('Code')">{{ $language->code }}</td>
                    <td class="px-4 py-3 text-gray-600" data-label="@lang('Direction')">{{ strtoupper($language->direction) }}</td>
                    <td class="px-4 py-3" data-label="@lang('Status')">
                        @if($language->is_active)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700">@lang('Active')</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-500">@lang('Disabled')</span>
                        @endif
                    </td>
                    <td class="px-4 py-3" data-label="@lang('Default')">
                        @if($language->is_default)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-700">@lang('Default')</span>
                        @else
                            <form method="POST" action="{{ route('admin.languages.default', $language) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-brand-600">@lang('Set default')</button>
                            </form>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$language->is_default)
                            <a href="{{ route('admin.translations.index', ['locale' => $language->code]) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-brand-200 bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-700 shadow-sm hover:bg-brand-100">
                                <x-heroicon-o-language class="h-3.5 w-3.5" />
                                @lang('Translate')
                            </a>
                            @endif
                            <a href="{{ route('admin.languages.edit', $language) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                                <x-heroicon-o-pencil class="h-3.5 w-3.5" />
                                @lang('Edit')
                            </a>
                            @if(!$language->is_default)
                                <form method="POST" action="{{ route('admin.languages.toggle', $language) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                                        {{ $language->is_active ? __('Disable') : __('Enable') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="{{ __('Delete :name?', ['name' => $language->name]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50">
                                        <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                        @lang('Delete')
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No languages found.')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

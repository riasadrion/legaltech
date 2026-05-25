@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('Pages') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $pages->count() }} {{ __('pages') }}</p>
        </div>
        <a href="{{ route('admin.cms.pages.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('New page') }}
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Slug') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sections') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pages as $page)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" data-label="{{ __('Title') }}">{{ $page->getLocalTitle() }}</td>
                    <td class="px-4 py-3 font-mono text-gray-500 text-xs" data-label="{{ __('Slug') }}">/{{ $page->slug }}</td>
                    <td class="px-4 py-3" data-label="{{ __('Status') }}">
                        @if($page->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Published') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">{{ __('Draft') }}</span>
                        @endif
                        @if($page->is_system)
                            <span class="ml-1 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600">{{ __('System') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500" data-label="{{ __('Sections') }}">{{ count($page->section_ids ?? []) }}</td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.cms.pages.edit', $page) }}"
                               class="text-gray-400 hover:text-brand-600" title="{{ __('Edit') }}">
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            @if(!$page->is_system)
                            <form method="POST" action="{{ route('admin.cms.pages.destroy', $page) }}">
                                @csrf @method('DELETE')
                                <button type="submit" data-confirm="{{ __('Delete this page?') }}"
                                        class="text-gray-400 hover:text-red-500" title="{{ __('Delete') }}">
                                    <x-heroicon-o-trash class="h-4 w-4" />
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No pages found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

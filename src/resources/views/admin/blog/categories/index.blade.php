@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold text-gray-900">@lang('Blog Categories')</h1>
        <a href="{{ route('admin.blog.categories.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('New category')
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Name')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Slug')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Posts')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" :data-label="__('Name')">{{ $category->getLocalName() }}</td>
                    <td class="px-4 py-3 font-mono text-gray-500 text-xs" :data-label="__('Slug')">{{ $category->slug }}</td>
                    <td class="px-4 py-3 text-gray-500" :data-label="__('Posts')">{{ $category->posts_count }}</td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.blog.categories.edit', $category) }}"
                               class="text-gray-400 hover:text-brand-600">
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('admin.blog.categories.destroy', $category) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" :data-confirm="__('Delete this category?')"
                                        class="text-gray-400 hover:text-red-500">
                                    <x-heroicon-o-trash class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No categories found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

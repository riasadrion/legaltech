@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Contract Categories')</h1>
            <p class="mt-0.5 text-sm text-gray-500">@lang('Organise contract templates into categories.')</p>
        </div>
        <a href="{{ route('admin.contracts.categories.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('New Category')
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Name')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Slug')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Templates')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Sort')</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $category->slug }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $category->templates_count }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $category->sort }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contracts.categories.edit', $category) }}"
                               class="inline-flex items-center rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-300 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600">@lang('Edit')</a>
                            <form method="POST" action="{{ route('admin.contracts.categories.destroy', $category) }}"
                                  onsubmit="return confirm('@lang('Delete this category?')')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs font-medium">@lang('Delete')</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">@lang('No categories yet.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

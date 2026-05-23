@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('Contract Categories') }}</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ __('Organise contract templates into categories.') }}</p>
        </div>
        <a href="{{ route('admin.contracts.categories.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('New Category') }}
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Slug') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Templates') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sort') }}</th>
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
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contracts.categories.edit', $category) }}"
                               class="text-brand-600 hover:underline text-xs font-medium">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.contracts.categories.destroy', $category) }}"
                                  onsubmit="return confirm('{{ __('Delete this category?') }}')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs font-medium">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">{{ __('No categories yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

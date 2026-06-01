@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Contract Templates')</h1>
            <p class="mt-0.5 text-sm text-gray-500">@lang('Manage AI-powered contract templates.')</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.contracts.categories.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                @lang('Categories')
            </a>
            <a href="{{ route('admin.contracts.templates.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-plus class="h-4 w-4" />
                @lang('New Template')
            </a>
        </div>
    </div>


    <form method="GET" class="flex flex-wrap items-center gap-2">
        <select name="category" onchange="this.form.submit()"
                class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none">
            <option value="">@lang('All categories')</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="@lang('Search templates…')"
               class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none" />
        <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700">@lang('Search')</button>
        @if(request()->hasAny(['q', 'category']))
            <a href="{{ route('admin.contracts.templates.index') }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
        @endif
    </form>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Name')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Category')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Credits')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $template->name }}</div>
                        @if($template->description)
                        <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $template->description }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $template->category->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $template->credits_cost }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $template->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $template->active ? __('Active') : __('Inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('admin.contracts.templates.toggle', $template) }}">
                                @csrf
                                <button class="text-xs font-medium {{ $template->active ? 'text-gray-400 hover:text-gray-700' : 'text-brand-600 hover:text-brand-800' }}">
                                    {{ $template->active ? __('Deactivate') : __('Activate') }}
                                </button>
                            </form>
                            <a href="{{ route('admin.contracts.templates.edit', $template) }}"
                               class="text-brand-600 hover:underline text-xs font-medium">@lang('Edit')</a>
                            <form method="POST" action="{{ route('admin.contracts.templates.destroy', $template) }}"
                                  onsubmit="return confirm('@lang('Delete this template?')')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs font-medium">@lang('Delete')</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">@lang('No templates yet. Create your first one.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($templates->hasPages())
    <div>{{ $templates->links() }}</div>
    @endif

</div>
@endsection

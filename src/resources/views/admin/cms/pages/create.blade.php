@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl space-y-5">
    <form method="POST" action="{{ route('admin.cms.pages.store') }}" class="space-y-5">
        @csrf

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.cms.pages.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <h1 class="text-xl font-bold text-gray-900">@lang('New Page')</h1>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5 mt-5">
            <x-admin::form.input name="title[en]" :label="__('Title')" :value="old('title.en')" required />

            <x-admin::form.input name="slug" :label="__('Slug')" placeholder="{{ __('e.g. about-us') }}" :value="old('slug')" required
                :hint="__('Lowercase letters, numbers, hyphens and slashes only. Used in the URL: /your-slug')" />

            <x-admin::form.select name="status" :label="__('Status')">
                <option value="draft" @selected(old('status', 'draft') === 'draft')>@lang('Draft')</option>
                <option value="published" @selected(old('status') === 'published')>@lang('Published')</option>
            </x-admin::form.select>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                @lang('Create page')
            </button>
            <a href="{{ route('admin.cms.pages.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                @lang('Cancel')
            </a>
        </div>
    </form>
</div>
@endsection

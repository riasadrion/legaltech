@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl space-y-5">
    <form method="POST" action="{{ route('admin.blog.categories.update', $category) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.blog.categories.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <h1 class="text-xl font-bold text-gray-900">Edit: {{ $category->getLocalName() }}</h1>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5 mt-5">
            <x-form.input name="name[en]" label="Name" :value="old('name.en', $category->name['en'] ?? '')" required />
            <x-form.input name="slug" label="Slug" :value="old('slug', $category->slug)" />
            <x-form.input name="sort" label="Sort order" type="number" :value="old('sort', $category->sort)" />
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Save changes
            </button>
            <a href="{{ route('admin.blog.categories.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

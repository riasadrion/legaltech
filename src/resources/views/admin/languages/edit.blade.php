@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl">
    <x-page-header title="Edit Language" subtitle="{{ $language->name }} ({{ $language->code }})" />

    <form method="POST" action="{{ route('admin.languages.update', $language) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 space-y-5">
            <x-form.input name="name" label="Language Name (English)" :value="old('name', $language->name)" required />
            <x-form.input name="native_name" label="Native Name" :value="old('native_name', $language->native_name)" required />

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500">Code</label>
                <p class="text-sm font-mono text-gray-900">{{ $language->code }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Language code cannot be changed after creation.</p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Text Direction</label>
                <select name="direction" class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="ltr" @selected(old('direction', $language->direction) === 'ltr')>LTR — Left to Right</option>
                    <option value="rtl" @selected(old('direction', $language->direction) === 'rtl')>RTL — Right to Left</option>
                </select>
            </div>

            <x-form.input name="sort" label="Sort Order" type="number" :value="old('sort', $language->sort)" />

            @if(!$language->is_default)
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $language->is_active)) class="rounded border-gray-300 text-brand-600">
                Active (visible to users)
            </label>
            @else
            <p class="text-xs text-gray-400">The default language is always active.</p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Save
            </button>
            <a href="{{ route('admin.languages.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

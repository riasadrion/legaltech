@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-5">

    <form method="POST" action="{{ route('admin.cms.sections.update', [$page, $section]) }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.cms.pages.edit', $page) }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit: {{ config('sections.' . $section->type . '.label', $section->type) }}</h1>
                <p class="text-sm text-gray-400">{{ $page->getLocalTitle() }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5 mt-5">


            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <label class="text-sm font-medium text-gray-700">Enabled</label>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="enabled" value="0">
                    <input type="checkbox" name="enabled" value="1" class="peer sr-only"
                           {{ old('enabled', $section->enabled) ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:bg-brand-600 peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            @php
                $d = $section->data ?? [];
                $rawSchema = config('sections.' . $section->type . '.fields');
                $schema = is_array($rawSchema) ? \App\Domain\Cms\SchemaNormalizer::normalize($rawSchema) : null;
            @endphp

            @if(is_array($schema))
                <x-cms.schema :schema="$schema" :data="$d" name-prefix="data" />

            @elseif($section->type === 'content')
                <x-form.input name="data[heading]" label="Heading" :value="old('data.heading', $d['heading'] ?? '')" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Body</label>
                    <input type="hidden" name="data[body]" id="section_body" value="{{ old('data.body', $d['body'] ?? '') }}">
                    <div class="prose-editor-init" data-input="section_body"></div>
                </div>

            @else
                <p class="text-sm text-gray-500">No editable fields for this section type.</p>
            @endif
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Save section
            </button>
            <a href="{{ route('admin.cms.pages.edit', $page) }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/editor.css') }}">
<script src="{{ asset('assets/static/js/editor.js') }}"></script>
@endpush

@push('scripts')
<script>

    'use strict';
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el)
    })
})
</script>
@include('components.cms.repeater-script')
@endpush

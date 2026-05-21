@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-5">

    <form method="POST" action="{{ route('admin.cms.sections.update', [$page, $section]) }}" class="space-y-5">
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

            {{-- Enabled toggle --}}
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
                $localeData = $section->data;
                $locale = 'en';
                $d = $localeData[$locale] ?? [];
            @endphp

            @if($section->type === 'hero')
                <x-form.input name="data[en][heading]"    label="Heading"    :value="old('data.en.heading',    $d['heading']    ?? '')" />
                <x-form.input name="data[en][subheading]" label="Subheading" :value="old('data.en.subheading', $d['subheading'] ?? '')" />
                <x-form.input name="data[en][cta_text]"   label="CTA text"   :value="old('data.en.cta_text',   $d['cta_text']   ?? '')" />
                <x-form.input name="data[en][cta_url]"    label="CTA URL"    :value="old('data.en.cta_url',    $d['cta_url']    ?? '')" />

            @elseif($section->type === 'cta')
                <x-form.input name="data[en][heading]"        label="Heading"         :value="old('data.en.heading',        $d['heading']        ?? '')" />
                <x-form.input name="data[en][body]"           label="Body"            :value="old('data.en.body',           $d['body']           ?? '')" />
                <x-form.input name="data[en][primary_text]"   label="Primary button"  :value="old('data.en.primary_text',   $d['primary_text']   ?? '')" />
                <x-form.input name="data[en][primary_url]"    label="Primary URL"     :value="old('data.en.primary_url',    $d['primary_url']    ?? '')" />
                <x-form.input name="data[en][secondary_text]" label="Secondary button":value="old('data.en.secondary_text', $d['secondary_text'] ?? '')" />
                <x-form.input name="data[en][secondary_url]"  label="Secondary URL"   :value="old('data.en.secondary_url',  $d['secondary_url']  ?? '')" />

            @elseif($section->type === 'cta_strip')
                <x-form.input name="data[en][text]"     label="Text"     :value="old('data.en.text',     $d['text']     ?? '')" />
                <x-form.input name="data[en][cta_text]" label="CTA text" :value="old('data.en.cta_text', $d['cta_text'] ?? '')" />
                <x-form.input name="data[en][cta_url]"  label="CTA URL"  :value="old('data.en.cta_url',  $d['cta_url']  ?? '')" />

            @elseif($section->type === 'content')
                <x-form.input name="data[en][heading]" label="Heading" :value="old('data.en.heading', $d['heading'] ?? '')" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Body</label>
                    <input type="hidden" name="data[en][body]" id="section_body" value="{{ old('data.en.body', $d['body'] ?? '') }}">
                    <div class="prose-editor-init" data-input="section_body"></div>
                </div>

            @elseif($section->type === 'custom_html')
                <x-form.input name="data[en][title]" label="Block title" :value="old('data.en.title', $d['title'] ?? '')" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">HTML body</label>
                    <textarea name="data[en][body]" rows="10"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('data.en.body', $d['body'] ?? '') }}</textarea>
                </div>
                @can('cms.custom_html.raw')
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="data[trust_raw]" value="1"
                           @checked(old('data.trust_raw', $localeData['trust_raw'] ?? false))
                           class="rounded border-gray-300 text-brand-600">
                    Trust HTML as-is (skip purifier)
                </label>
                @endcan

            @elseif($section->type === 'features')
                <x-form.input name="data[en][heading]" label="Heading" :value="old('data.en.heading', $d['heading'] ?? '')" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Features (JSON)</label>
                    <textarea name="data[en][features]" rows="6"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('data.en.features', json_encode($d['features'] ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Array of {icon, title, body}</p>
                </div>

            @elseif($section->type === 'stats')
                <x-form.input name="data[en][heading]" label="Heading" :value="old('data.en.heading', $d['heading'] ?? '')" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Stats (JSON)</label>
                    <textarea name="data[en][stats]" rows="6"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('data.en.stats', json_encode($d['stats'] ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Array of {value, label}</p>
                </div>

            @elseif($section->type === 'latest_blog_posts')
                <x-form.input name="data[en][heading]" label="Heading" :value="old('data.en.heading', $d['heading'] ?? '')" />
                <x-form.input name="data[count]" label="Number of posts" type="number"
                    :value="old('data.count', $localeData['count'] ?? 3)" />

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
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
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
onAdminReady(function () {
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el)
    })
})
</script>
@endpush

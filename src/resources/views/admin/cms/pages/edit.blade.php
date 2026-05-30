@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div class="flex items-center gap-3">
        <a href="{{ route('admin.cms.pages.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">{{ $page->getLocalTitle() }}</h1>
            <p class="text-sm text-gray-400 font-mono">/{{ $page->slug }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:items-start">


        <div class="lg:col-span-2 space-y-4">


            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-3.5 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Sections') }}</h3>
                    <span class="text-xs text-gray-400">{{ __('Drag to reorder') }}</span>
                </div>

                @if($sections->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    {{ __('No sections yet. Add one below.') }}
                </div>
                @else
                <ul id="sections-sortable" class="divide-y divide-gray-100">
                    @foreach($sections as $section)
                    <li class="flex items-center justify-between gap-3 px-5 py-3" data-id="{{ $section->id }}">
                        <span class="cursor-grab text-gray-300 hover:text-gray-400 flex-shrink-0" data-drag-handle>
                            <x-heroicon-o-bars-2 class="h-5 w-5" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ config('sections.' . $section->type . '.label', $section->type) }}
                            </p>
                            <p class="text-xs text-gray-400 font-mono">{{ $section->type }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="flex items-center gap-2">
                                @if($section->enabled)
                                    <span class="inline-flex h-2 w-2 rounded-full bg-green-400 mt-0.75"></span>
                                @else
                                    <span class="inline-flex h-2 w-2 rounded-full bg-gray-300 mt-0.75"></span>
                                @endif
                                <a href="{{ route('admin.cms.sections.edit', [$page, $section]) }}"
                                   class="text-xs text-brand-600 hover:text-brand-700 font-medium mt-0.75">{{ __('Edit') }}</a>
                            </div>
                            <form method="POST" action="{{ route('admin.cms.sections.toggle', [$page, $section]) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">
                                    {{ $section->enabled ? __('Disable') : __('Enable') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.cms.sections.detach', [$page, $section]) }}" class="inline">
                                @csrf
                                <button type="submit" data-confirm="{{ __('Detach this section from the page?') }}"
                                        class="text-xs text-amber-500 hover:text-amber-700">{{ __('Detach') }}</button>
                            </form>
                            @if($section->type === 'custom_html')
                                <form method="POST" action="{{ route('admin.cms.sections.destroy', [$page, $section]) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-confirm="{{ __('Permanently delete this section?') }}"
                                            class="text-xs text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>


            @if($availableTypes->isNotEmpty() || $reusableSections->isNotEmpty())
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Add section') }}</h3>
                </div>
                <div class="px-5 py-4">
                    <form method="POST" action="{{ route('admin.cms.sections.store', $page) }}" class="flex items-center gap-3">
                        @csrf
                        <select name="source"
                                class="flex-1 h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            @if($availableTypes->isNotEmpty())
                                <optgroup label="{{ __('Create new') }}">
                                    @foreach($availableTypes as $type => $cfg)
                                        <option value="{{ $type }}">{{ $cfg['label'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($reusableSections->isNotEmpty())
                                <optgroup label="{{ __('Reuse existing') }}">
                                    @foreach($reusableSections as $existing)
                                        @php
                                            $label = config('sections.' . $existing->type . '.label', $existing->type);
                                            $hint = $existing->data['heading'] ?? $existing->data['heading_line_1'] ?? $existing->data['title'] ?? '';
                                            $hint = is_string($hint) ? trim(strip_tags($hint)) : '';
                                        @endphp
                                        <option value="existing:{{ $existing->id }}">
                                            {{ $label }} #{{ $existing->id }}{{ $hint ? ' — ' . \Illuminate\Support\Str::limit($hint, 50) : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            {{ __('Add') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>


        <div>
            <form method="POST" action="{{ route('admin.cms.pages.update', $page) }}" class="space-y-4">
                @csrf @method('PUT')

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-5 py-3.5">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Page settings') }}</h3>
                    </div>
                    <div class="px-5 py-5 space-y-4">
                        <x-form.input name="slug" label="{{ __('Slug') }}" :value="old('slug', $page->slug)"
                            :required="true" :readonly="$page->is_system" />

                        <x-form.input name="title[en]" label="{{ __('Title') }}"
                            :value="old('title.en', $page->title['en'] ?? '')" required />

                        <x-form.input name="meta_title[en]" label="{{ __('Meta title') }}"
                            :value="old('meta_title.en', $page->meta_title['en'] ?? '')" />

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Meta description') }}</label>
                            <textarea name="meta_description[en]" rows="2"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('meta_description.en', $page->meta_description['en'] ?? '') }}</textarea>
                        </div>

                        <x-form.select name="status" label="{{ __('Status') }}">
                            <option value="draft" @selected(old('status', $page->status) === 'draft')>{{ __('Draft') }}</option>
                            <option value="published" @selected(old('status', $page->status) === 'published')>{{ __('Published') }}</option>
                        </x-form.select>
                    </div>
                    <div class="border-t border-gray-100 px-5 py-4 flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                            <x-heroicon-o-check class="h-4 w-4" />
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>

    'use strict';
    var el = document.getElementById('sections-sortable');
    if (!el || typeof Sortable === 'undefined') return;

    Sortable.create(el, {
        handle: '[data-drag-handle]',
        animation: 150,
        onEnd: function () {
            var order = Array.from(el.querySelectorAll('[data-id]')).map(function (li) {
                return parseInt(li.dataset.id, 10);
            });
            fetch('{{ route('admin.cms.sections.reorder', $page) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ order: order }),
            }).then(function (res) {
                if (res.ok && typeof window.toast === 'function') {
                    window.toast('{{ __('Order saved') }}', 'success');
                }
            });
        },
    });
});
</script>
@endpush
@endsection

{{-- Basic details --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-4">
    <h2 class="font-semibold text-gray-800">{{ __('Template Details') }}</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}"
                   class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }} <span class="text-red-500">*</span></label>
            <select name="category_id" class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required>
                <option value="">{{ __('Select category…') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $template->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
        <input type="text" name="description" value="{{ old('description', $template->description ?? '') }}"
               placeholder="{{ __('Brief description shown to users…') }}"
               class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Credits Cost') }}</label>
            <input type="number" name="credits_cost" value="{{ old('credits_cost', $template->credits_cost ?? 1) }}" min="0"
                   class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            @error('credits_cost')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sort') }}</label>
            <input type="number" name="sort" value="{{ old('sort', $template->sort ?? 0) }}"
                   class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        </div>
        <div class="flex items-end pb-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" {{ old('active', $template->active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                <span class="text-sm font-medium text-gray-700">{{ __('Active') }}</span>
            </label>
        </div>
    </div>
</div>

{{-- Template body --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
    <div>
        <h2 class="font-semibold text-gray-800">{{ __('Contract Body') }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">
            {{ __('Use') }} <code class="bg-gray-100 px-1 rounded">{{variable_key}}</code> {{ __('for fixed placeholders and') }}
            <code class="bg-gray-100 px-1 rounded">[AI_SECTION: your prompt here]</code> {{ __('for AI-generated sections.') }}
        </p>
    </div>
    <textarea name="body" rows="18"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
              required>{{ old('body', $template->body ?? '') }}</textarea>
    @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

{{-- AI system prompt --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
    <div>
        <h2 class="font-semibold text-gray-800">{{ __('AI System Prompt') }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ __('Instructions for the AI on tone, style, and jurisdiction. Leave blank for the default legal drafting prompt.') }}</p>
    </div>
    <textarea name="ai_system_prompt" rows="4"
              placeholder="{{ __('e.g. You are a US-based commercial contract lawyer. Write in formal English with clear numbered clauses…') }}"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('ai_system_prompt', $template->ai_system_prompt ?? '') }}</textarea>
</div>

{{-- Dynamic fields builder --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">{{ __('Wizard Fields') }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Fields shown to users when generating a contract from this template.') }}</p>
        </div>
        <button type="button" id="add-field-btn"
                class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 px-3 py-1.5 text-sm font-medium text-brand-600 hover:bg-brand-50">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('Add Field') }}
        </button>
    </div>

    <div id="fields-list" class="space-y-3">
        @php $existingFields = old('fields', isset($template) ? $template->fields->toArray() : []); @endphp
        @foreach($existingFields as $i => $field)
        @include('admin.contracts.templates._field_row', ['i' => $i, 'field' => $field])
        @endforeach
    </div>

    <div id="fields-empty" class="{{ count($existingFields) > 0 ? 'hidden' : '' }} text-sm text-gray-400 text-center py-4 border border-dashed border-gray-200 rounded-lg">
        {{ __('No fields yet — click "Add Field" to define what users fill in.') }}
    </div>
</div>

@push('scripts')
<script>
(function () {
    var idx = {{ count($existingFields) }};

    document.getElementById('add-field-btn').addEventListener('click', function () {
        var list = document.getElementById('fields-list');
        var empty = document.getElementById('fields-empty');
        var tpl = document.getElementById('field-row-template').innerHTML
            .replace(/__IDX__/g, idx);
        list.insertAdjacentHTML('beforeend', tpl);
        empty.classList.add('hidden');
        idx++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-field-btn')) {
            e.target.closest('.field-row').remove();
            if (!document.querySelector('.field-row')) {
                document.getElementById('fields-empty').classList.remove('hidden');
            }
        }
        // Show/hide options textarea based on type
        if (e.target.matches('.field-type-select')) {
            var row = e.target.closest('.field-row');
            var optionsWrap = row.querySelector('.options-wrap');
            if (optionsWrap) {
                optionsWrap.classList.toggle('hidden', e.target.value !== 'select');
            }
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.matches('.field-type-select')) {
            var row = e.target.closest('.field-row');
            var optionsWrap = row.querySelector('.options-wrap');
            if (optionsWrap) {
                optionsWrap.classList.toggle('hidden', e.target.value !== 'select');
            }
        }
    });
})();
</script>

<template id="field-row-template">
    @include('admin.contracts.templates._field_row', ['i' => '__IDX__', 'field' => []])
</template>
@endpush

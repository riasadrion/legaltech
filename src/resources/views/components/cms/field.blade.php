@props([
    'field',     // schema entry — ['key' => ..., 'type' => ..., ...]
    'value' => null,    // current value for this field (scalar, array, or null)
    'namePrefix',       // e.g. "data[en]" or "data[en][logos][0]"
])

@php
    $type = $field['type'];
    $key = $field['key'] ?? null;
    $label = $field['label'] ?? null;
    $hint = $field['hint'] ?? null;
    $name = $key !== null ? $namePrefix . '[' . $key . ']' : $namePrefix;
    $domId = str_replace(['[', ']'], ['-', ''], $name);
@endphp

@switch($type)

    @case('text')
    @case('url')
        <x-form.input :name="$name" :label="$label" :value="$value ?? ''" :hint="$hint" />
        @break

    @case('textarea')
        <div class="space-y-1">
            @if($label)<label class="block text-sm font-medium text-gray-700">{{ $label }}</label>@endif
            <textarea name="{{ $name }}" rows="3"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old($name, $value ?? '') }}</textarea>
            @if($hint)<p class="text-xs text-gray-500">{{ $hint }}</p>@endif
        </div>
        @break

    @case('richtext')
        <div class="space-y-1">
            @if($label)<label class="block text-sm font-medium text-gray-700">{{ $label }}</label>@endif
            <input type="hidden" name="{{ $name }}" id="{{ $domId }}" value="{{ old($name, $value ?? '') }}">
            <div class="prose-editor-init" data-input="{{ $domId }}"></div>
        </div>
        @break

    @case('checkbox')
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" name="{{ $name }}" value="1"
                class="rounded border-gray-300 text-brand-600"
                {{ old($name, $value) ? 'checked' : '' }}>
            {{ $label }}
        </label>
        @break

    @case('select')
        <div class="space-y-1">
            @if($label)<label class="block text-sm font-medium text-gray-700">{{ $label }}</label>@endif
            <select name="{{ $name }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                @foreach(($field['options'] ?? []) as $optValue => $optLabel)
                    <option value="{{ $optValue }}" @selected(old($name, $value) === $optValue)>{{ $optLabel }}</option>
                @endforeach
            </select>
        </div>
        @break

    @case('image')
        <div class="space-y-2">
            @if($label)<label class="block text-sm font-medium text-gray-700">{{ $label }}</label>@endif
            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                <div class="flex h-16 w-32 shrink-0 items-center justify-center rounded bg-gray-900 px-3 text-[10px] text-white/40">
                    @if(!empty($value))
                        <img src="{{ upload_url($value) }}" alt="" class="max-h-12 max-w-full object-contain">
                    @else
                        IMG
                    @endif
                </div>
                <input type="hidden" name="{{ $name }}" value="{{ $value ?? '' }}">
                <label class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">
                    {{ !empty($value) ? 'Replace' : 'Upload' }}
                    <input type="file" name="{{ $name }}_file" accept=".png,.jpg,.jpeg,.webp" class="hidden">
                </label>
            </div>
            @if($hint)<p class="text-xs text-gray-500">{{ $hint }}</p>@endif
        </div>
        @break

    @case('group')
        <fieldset class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-4">
            @if($label)
                <legend class="px-2 text-sm font-medium text-gray-700">{{ $label }}</legend>
            @endif
            <x-cms.schema :schema="$field['fields']" :data="$value ?? []" :name-prefix="$name" />
        </fieldset>
        @break

    @case('repeater')
        @php
            $items = is_array($value) ? array_values($value) : [];
            $itemFields = $field['item_fields'] ?? [];
            $addLabel = $field['add_label'] ?? ('Add ' . strtolower($label ?? 'item'));
            $repeaterId = $domId . '-repeater';
        @endphp
        <div class="space-y-2">
            @if($label)<label class="block text-sm font-medium text-gray-700">{{ $label }}</label>@endif
            @if($hint)<p class="text-xs text-gray-500">{{ $hint }}</p>@endif

            <div id="{{ $repeaterId }}" class="space-y-3" data-name-prefix="{{ $name }}">
                @foreach($items as $i => $item)
                    <div class="repeater-row rounded-lg border border-gray-200 bg-white p-4" data-index="{{ $i }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 space-y-3">
                                <x-cms.schema :schema="$itemFields" :data="$item" :name-prefix="$name . '[' . $i . ']'" />
                            </div>
                            <button type="button" class="repeater-remove rounded-lg border border-red-200 bg-white px-2.5 py-2 text-red-600 hover:bg-red-50">
                                <x-heroicon-o-trash class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Hidden template row: rendered with placeholder __INDEX__ that JS substitutes --}}
            <template id="{{ $repeaterId }}-template">
                <div class="repeater-row rounded-lg border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 space-y-3">
                            <x-cms.schema :schema="$itemFields" :data="[]" :name-prefix="$name . '[__INDEX__]'" />
                        </div>
                        <button type="button" class="repeater-remove rounded-lg border border-red-200 bg-white px-2.5 py-2 text-red-600 hover:bg-red-50">
                            <x-heroicon-o-trash class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" data-repeater-add="{{ $repeaterId }}"
                class="inline-flex items-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                <x-heroicon-o-plus class="h-4 w-4" />
                {{ $addLabel }}
            </button>
        </div>
        @break

@endswitch

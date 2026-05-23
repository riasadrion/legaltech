@php
    $fieldType    = $field['type'] ?? 'text';
    $fieldOptions = is_array($field['options'] ?? null) ? implode("\n", $field['options']) : ($field['options'] ?? '');
@endphp
<div class="field-row rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Field') }} #{{ is_numeric($i) ? $i + 1 : '?' }}</span>
        <button type="button" class="remove-field-btn text-red-400 hover:text-red-600">
            <x-heroicon-o-trash class="h-4 w-4" />
        </button>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Label') }}</label>
            <input type="text" name="fields[{{ $i }}][label]" value="{{ $field['label'] ?? '' }}"
                   placeholder="{{ __('e.g. Client Name') }}"
                   class="h-8 w-full rounded-md border border-gray-300 px-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Key') }}</label>
            <input type="text" name="fields[{{ $i }}][key]" value="{{ $field['key'] ?? '' }}"
                   placeholder="{{ __('e.g. client_name') }}"
                   class="h-8 w-full rounded-md border border-gray-300 px-2 text-sm font-mono focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Type') }}</label>
            <select name="fields[{{ $i }}][type]"
                    class="field-type-select h-8 w-full rounded-md border border-gray-300 px-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                @foreach(['text','textarea','date','number','select','email'] as $t)
                    <option value="{{ $t }}" {{ $fieldType === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Group') }}</label>
            <input type="text" name="fields[{{ $i }}][group]" value="{{ $field['group'] ?? '' }}"
                   placeholder="{{ __('e.g. Party Details') }}"
                   class="h-8 w-full rounded-md border border-gray-300 px-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Placeholder') }}</label>
            <input type="text" name="fields[{{ $i }}][placeholder]" value="{{ $field['placeholder'] ?? '' }}"
                   class="h-8 w-full rounded-md border border-gray-300 px-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
        </div>
    </div>

    <div class="options-wrap {{ $fieldType !== 'select' ? 'hidden' : '' }}">
        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Options') }} <span class="text-gray-400">({{ __('one per line') }})</span></label>
        <textarea name="fields[{{ $i }}][options]" rows="3"
                  class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm font-mono focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ $fieldOptions }}</textarea>
    </div>

    <div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="fields[{{ $i }}][required]" value="0">
            <input type="checkbox" name="fields[{{ $i }}][required]" value="1"
                   {{ ($field['required'] ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
            <span class="text-xs font-medium text-gray-600">{{ __('Required') }}</span>
        </label>
    </div>
</div>

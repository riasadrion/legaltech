@props(['form', 'old' => [], 'errors' => null])


@foreach($form->fields as $field)
@php
    $label       = $field->getLocaleLabel();
    $placeholder = $field->getLocalePlaceholder() ?? '';
    $oldVal      = $old[$field->name] ?? old($field->name, '');
    $errMsg      = $errors?->first($field->name);
    $baseClass   = 'w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 ' . ($errMsg ? 'border-red-400' : 'border-gray-300');
@endphp

<div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if($field->required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>

    @if($field->type === 'textarea')
        <textarea name="{{ $field->name }}"
                  placeholder="{{ $placeholder }}"
                  rows="3"
                  @if($field->required) required @endif
                  class="{{ $baseClass }} py-2">{{ $oldVal }}</textarea>

    @elseif($field->type === 'select')
        <select name="{{ $field->name }}"
                @if($field->required) required @endif
                class="{{ $baseClass }} h-11">
            <option value="">— Select —</option>
            @foreach($field->options ?? [] as $option)
                <option value="{{ $option['value'] }}" @selected($oldVal == $option['value'])>
                    {{ $option['label'] ?? $option['value'] }}
                </option>
            @endforeach
        </select>

    @elseif($field->type === 'file')
        <input type="file" name="{{ $field->name }}"
               @if($field->required) required @endif
               class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" />

    @elseif($field->type === 'checkbox')
        <div class="flex items-center gap-2">
            <input type="checkbox" name="{{ $field->name }}" value="1" id="field_{{ $field->name }}"
                   @checked($oldVal)
                   @if($field->required) required @endif
                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
            <label for="field_{{ $field->name }}" class="text-sm text-gray-700">{{ $placeholder ?: $label }}</label>
        </div>

    @else
        <input type="{{ $field->type }}" name="{{ $field->name }}"
               value="{{ $oldVal }}"
               placeholder="{{ $placeholder }}"
               @if($field->required) required @endif
               class="{{ $baseClass }} h-11" />
    @endif

    @if($errMsg)
        <p class="text-xs text-red-600">{{ $errMsg }}</p>
    @endif
</div>
@endforeach

@props(['label' => '', 'name', 'rows' => 4, 'required' => false, 'hint' => ''])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <textarea id="{{ $name }}"
              name="{{ $name }}"
              rows="{{ $rows }}"
              @if ($required) required @endif
              {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 ' . ($errors->has($name) ? 'border-red-400' : '')]) }}>{{ old($name, $slot) }}</textarea>

    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror

    @if ($hint && !$errors->has($name))
        <p class="text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>

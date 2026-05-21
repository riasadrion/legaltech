@props(['label', 'name', 'value' => '1', 'checked' => false, 'hint' => ''])

<div class="flex items-start gap-3">
    <input type="checkbox"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           @checked(old($name, $checked))
           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
    <div class="space-y-0.5">
        <label for="{{ $name }}" class="text-sm font-medium text-gray-700 cursor-pointer">{{ $label }}</label>
        @if ($hint)
            <p class="text-xs text-gray-500">{{ $hint }}</p>
        @endif
        @error($name)
            <p class="text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

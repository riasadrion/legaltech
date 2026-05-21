@props(['label', 'name', 'type' => 'text', 'placeholder' => ''])

<div>
    <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ $label }}</label>
    <input type="{{ $type }}"
           name="credentials[{{ $name }}]"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('credentials.' . $name) ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
    @error('credentials.' . $name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

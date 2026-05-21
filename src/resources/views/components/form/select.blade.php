@props(['label' => '', 'name', 'options' => null, 'selected' => '', 'required' => false, 'placeholder' => ''])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <select id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 ' . ($errors->has($name) ? 'border-red-400' : '')]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if (is_array($options))
            @foreach ($options as $val => $lbl)
                <option value="{{ $val }}" @selected(old($name, $selected) == $val)>{{ $lbl }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

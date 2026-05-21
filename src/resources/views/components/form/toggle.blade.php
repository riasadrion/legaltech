@props([
    'name',
    'value' => '1',
    'checked' => false,
])

<label class="relative inline-flex items-center cursor-pointer shrink-0">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }} class="sr-only peer" {{ $attributes }}>
    <div class="w-11 h-6 rounded-full transition-colors duration-200
                bg-gray-300 dark:bg-slate-600
                peer-checked:bg-brand-600 dark:peer-checked:bg-brand-500
                peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2
                relative
                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                after:bg-white after:rounded-full after:h-5 after:w-5
                after:shadow-sm after:transition-transform after:duration-200
                peer-checked:after:translate-x-5">
    </div>
</label>

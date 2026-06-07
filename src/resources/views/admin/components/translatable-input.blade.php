@props(['name', 'label' => '', 'type' => 'input', 'rows' => 4, 'values' => []])

@php
    $languages = \App\Models\Language::where('is_active', true)->orderBy('sort')->get();
    $firstCode = $languages->first()?->code ?? 'en';
@endphp

<div class="translatable-input space-y-2">
    @if ($label)
        <span class="block text-sm font-medium text-gray-700">{{ $label }}</span>
    @endif


    <div class="flex gap-1.5">
        @foreach ($languages as $lang)
            <button type="button"
                    class="locale-tab rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors
                           {{ $loop->first ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600' }}"
                    data-locale="{{ $lang->code }}"
                    data-target="{{ $name }}">
                {{ strtoupper($lang->code) }}
            </button>
        @endforeach
    </div>


    @foreach ($languages as $lang)
        <div class="locale-panel {{ $loop->first ? '' : 'hidden' }}" data-locale="{{ $lang->code }}" data-field="{{ $name }}">
            @if ($type === 'textarea')
                <textarea name="{{ $name }}[{{ $lang->code }}]"
                          rows="{{ $rows }}"
                          dir="{{ $lang->direction }}"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ $values[$lang->code] ?? '' }}</textarea>
            @else
                <input type="text"
                       name="{{ $name }}[{{ $lang->code }}]"
                       value="{{ $values[$lang->code] ?? '' }}"
                       dir="{{ $lang->direction }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            @endif
        </div>
    @endforeach
</div>

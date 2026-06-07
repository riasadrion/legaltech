@props([
    'items' => [],        // array of ['value' => '', 'label' => 'Label', 'count' => 0]
    'current' => '',      // currently selected value
    'param' => 'status',  // query param name
    'allLabel' => 'All',
    'color' => 'brand',   // active pill color: brand, gray
])

@php
$activeClass = $color === 'gray'
    ? 'bg-gray-800 dark:bg-gray-700 text-white'
    : 'bg-brand-600 dark:bg-brand-600 text-white';

$inactiveClass = 'bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800';
@endphp

<div class="flex flex-wrap gap-2">
    @foreach($items as $item)
        @php
            $value = $item['value'] ?? '';
            $label = $item['label'] ?? '';
            $count = $item['count'] ?? null;
            $isActive = (string) $current === (string) $value;
            $href = request()->fullUrlWithQuery([$param => $value]);
        @endphp
        <a href="{{ $href }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $isActive ? $activeClass : $inactiveClass }}">
            {{ $label }}
            @if($count !== null)
                <span class="ml-1.5 {{ $isActive ? 'text-white/70' : 'text-gray-400 dark:text-slate-500' }}">{{ number_format($count) }}</span>
            @endif
        </a>
    @endforeach
</div>

@props([
    'title',
    'value',
    'subtitle' => null,
    'icon',
    'color' => 'blue', // blue, green, amber, purple, red, cyan
    'href' => null,
])

@php
$colors = [
    'blue'   => ['from-blue-500 to-blue-600',   'hover:border-blue-200',   'from-blue-50'],
    'green'  => ['from-green-500 to-green-600',  'hover:border-green-200',  'from-green-50'],
    'amber'  => ['from-amber-500 to-amber-600',  'hover:border-amber-200',  'from-amber-50'],
    'purple' => ['from-purple-500 to-purple-600','hover:border-purple-200', 'from-purple-50'],
    'red'    => ['from-red-500 to-red-600',      'hover:border-red-200',    'from-red-50'],
    'cyan'   => ['from-cyan-500 to-cyan-600',    'hover:border-cyan-200',   'from-cyan-50'],
    'indigo' => ['from-indigo-500 to-indigo-600','hover:border-indigo-200', 'from-indigo-50'],
    'pink'   => ['from-pink-500 to-pink-600',    'hover:border-pink-200',   'from-pink-50'],
];

$colorSet = $colors[$color] ?? $colors['blue'];
$iconGradient = $colorSet[0];
$borderHover = $colorSet[1];
$bgHover = $colorSet[2];

$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="group relative rounded-2xl border border-gray-100 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 {{ $borderHover }} overflow-hidden {{ $href ? 'cursor-pointer' : '' }}"
>
    <div class="absolute inset-0 bg-gradient-to-br {{ $bgHover }} to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    <div class="relative flex items-center justify-between">
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-4 text-4xl font-bold text-gray-900 tracking-tight">{{ $value }}</p>
            @if($subtitle)
                <p class="mt-2 text-xs text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $iconGradient }} text-white flex-shrink-0 shadow-lg group-hover:shadow-xl transition-shadow ml-4">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-7 w-7" />
        </div>
    </div>
</{{ $tag }}>

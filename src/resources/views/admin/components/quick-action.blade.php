@props([
    'title',
    'subtitle',
    'icon',
    'color' => 'blue',
    'href' => '#',
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
@endphp

<a href="{{ $href }}" class="group relative rounded-2xl border border-gray-100 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 {{ $borderHover }} overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br {{ $bgHover }} to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
    <div class="relative flex items-center gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $iconGradient }} text-white shadow-lg group-hover:shadow-xl transition-shadow">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-7 w-7" />
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-900 text-sm">{{ $title }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $subtitle }}</p>
        </div>
    </div>
</a>

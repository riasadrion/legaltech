@props(['title', 'description' => '', 'icon' => 'heroicon-o-inbox'])

<div class="text-center py-12">
    <x-dynamic-component :component="$icon" class="mx-auto w-12 h-12 text-gray-300" />
    <h3 class="mt-4 text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif
    @if (isset($slot) && $slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>

@props(['title', 'description' => ''])

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex items-center gap-3">{{ $actions }}</div>
    @endif
</div>

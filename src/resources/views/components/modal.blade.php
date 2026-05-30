@props(['id', 'title' => '', 'size' => 'md'])

@php
$sizes = ['sm' => 'max-w-sm', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];
$maxW  = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}"
     class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog" aria-modal="true">

    <div class="modal-backdrop absolute inset-0 bg-black/50" data-modal-close="{{ $id }}"></div>


    <div class="relative w-full {{ $maxW }} bg-white dark:bg-slate-800 rounded-2xl shadow-xl">
        @if ($title)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-400 dark:hover:text-slate-300" data-modal-close="{{ $id }}">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        @endif
        <div class="p-6 dark:text-slate-200">
            {{ $slot }}
        </div>
    </div>
</div>

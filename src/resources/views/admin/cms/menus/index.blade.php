@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Menus') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('Manage navigation menus.') }}</p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        @foreach($menus as $menu)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5">
            <h3 class="font-semibold text-gray-900">{{ $menu->getLocalName() }}</h3>
            <p class="text-xs text-gray-400 font-mono mb-3">{{ $menu->handle }}</p>
            <p class="text-sm text-gray-500 mb-4">{{ $menu->allItems()->count() }} {{ __('items') }}</p>
            <a href="{{ route('admin.cms.menus.edit', $menu) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-pencil class="h-4 w-4" />
                {{ __('Edit menu') }}
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

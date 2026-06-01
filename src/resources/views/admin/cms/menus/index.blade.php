@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Menus')</h1>
        <p class="text-sm text-gray-500 mt-0.5">@lang('Manage navigation menus.')</p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        @foreach($menus as $menu)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5">
            <h3 class="font-semibold text-gray-900">{{ $menu->getLocalName() }}</h3>
            <p class="text-xs text-gray-400 font-mono mb-3">{{ $menu->handle }}</p>
            <p class="text-sm text-gray-500 mb-4">{{ $menu->allItems()->count() }} @lang('items')</p>
            <a href="{{ route('admin.cms.menus.edit', $menu) }}"
               class="btn-secondary">
                <x-heroicon-o-pencil class="h-4 w-4" />
                @lang('Edit menu')
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

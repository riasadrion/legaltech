@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('admin.cms.menus.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">{{ $menu->getLocalName() }}</h1>
    </div>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Items</h3>
            <span class="text-xs text-gray-400">Drag to reorder</span>
        </div>

        @if($items->isEmpty())
        <p class="px-5 py-6 text-sm text-gray-400 text-center">No items yet.</p>
        @else
        <ul id="menu-items-sortable" class="divide-y divide-gray-100">
            @foreach($items->whereNull('parent_id') as $item)
            <li class="flex items-center gap-3 px-5 py-3" data-id="{{ $item->id }}">
                <span class="cursor-grab text-gray-300 hover:text-gray-400">
                    <x-heroicon-o-bars-2 class="h-5 w-5" />
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $item->getLocalLabel() }}</p>
                    <p class="text-xs text-gray-400">{{ $item->resolvedUrl() }}</p>
                </div>
                <form method="POST" action="{{ route('admin.cms.menus.items.destroy', [$menu, $item]) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" data-confirm="Remove this item?"
                            class="text-xs text-red-500 hover:text-red-700">Remove</button>
                </form>
            </li>
            @endforeach
        </ul>
        @endif
    </div>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h3 class="text-sm font-semibold text-gray-900">Add item</h3>
        </div>
        <form method="POST" action="{{ route('admin.cms.menus.items.store', $menu) }}" class="px-5 py-5 space-y-4">
            @csrf
            <x-form.input name="label[en]" label="Label" :value="old('label.en')" required />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Link to page</label>
                    <select name="page_id" id="menu-page-select"
                            class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">— Custom URL —</option>
                        @foreach($pages as $page)
                        <option value="{{ $page->id }}" @selected(old('page_id') == $page->id)>
                            {{ $page->getLocalTitle() }} (/{{ $page->slug }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="custom-url-field">
                    <x-form.input name="url" label="Custom URL" placeholder="/contact" :value="old('url')" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="open_in_new_tab" value="1" @checked(old('open_in_new_tab'))
                       class="rounded border-gray-300 text-brand-600">
                Open in new tab
            </label>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-plus class="h-4 w-4" />
                Add item
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>

    'use strict';
    var el = document.getElementById('menu-items-sortable');
    if (el && typeof Sortable !== 'undefined') {
        Sortable.create(el, {
            handle: '[class*="cursor-grab"]',
            animation: 150,
            onEnd: function () {
                var order = Array.from(el.querySelectorAll('[data-id]')).map(function (li) {
                    return li.dataset.id;
                });
                fetch('{{ route('admin.cms.menus.reorder', $menu) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ order: order }),
                });
            },
        });
    }

    // Toggle custom URL field when page is selected
    $('#menu-page-select').on('change', function () {
        if ($(this).val()) {
            $('#custom-url-field').hide();
            $('#custom-url-field input').val('');
        } else {
            $('#custom-url-field').show();
        }
    }).trigger('change');
});
</script>
@endpush
@endsection

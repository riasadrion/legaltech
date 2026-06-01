@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('New Contract Template')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Describe the contract — the AI will draft the full body with the right placeholders, and wizard fields will be created automatically.')</p>
    </div>

    <form id="template-form" method="POST" action="{{ route('admin.contracts.templates.store') }}" class="space-y-5" data-no-page-loader>
        @csrf


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800">@lang('Template Details')</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Name') <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="@lang('e.g. Business Partnership Agreement')"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Category') <span class="text-red-500">*</span></label>
                    <select name="category_id" class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required>
                        <option value="">@lang('Select category…')</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Credits Cost')</label>
                    <input type="number" name="credits_cost" value="{{ old('credits_cost', 1) }}" min="0"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Sort')</label>
                    <input type="number" name="sort" value="{{ old('sort', 0) }}"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                        <span class="text-sm font-medium text-gray-700">@lang('Active')</span>
                    </label>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
            <div>
                <h2 class="font-semibold text-gray-800">@lang('Contract Description') <span class="text-red-500">*</span></h2>
                <p class="text-xs text-gray-400 mt-0.5">@lang('Briefly describe what kind of contract this is and any clauses it must include. The AI will draft the full body and decide which placeholders are needed.')</p>
            </div>
            <textarea name="description" rows="6" required
                      placeholder="@lang('e.g. A Business Partnership Agreement between two parties covering capital contributions, profit sharing, management, banking, term and termination, dispute resolution, and governing law.')"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4">
            <div class="flex items-start gap-3">
                <x-heroicon-o-sparkles class="h-5 w-5 text-brand-600 mt-0.5 shrink-0" />
                <p class="text-sm text-brand-900">{{ __('On save, the AI will generate the full contract body and extract wizard fields from its placeholders. You\'ll be taken to the edit screen to review and adjust.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                <x-heroicon-o-sparkles class="h-4 w-4" />
                @lang('Generate Template')
            </button>
            <a href="{{ route('admin.contracts.templates.index') }}" class="text-sm text-gray-500 hover:underline">@lang('Cancel')</a>
        </div>
    </form>

</div>


<div id="ai-loader" class="hidden fixed inset-0 z-[100] items-center justify-center bg-gray-900/80 backdrop-blur-sm">
    <div class="max-w-md w-full mx-4 rounded-2xl bg-white shadow-2xl p-8 text-center">
        <div class="relative mx-auto mb-6 h-20 w-20">
            <div class="absolute inset-0 rounded-full border-4 border-brand-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-brand-600 border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <x-heroicon-o-sparkles class="h-9 w-9 text-brand-600 animate-pulse" />
            </div>
        </div>
        <h2 class="text-lg font-bold text-gray-900">@lang('Drafting your contract template…')</h2>
        <p id="ai-loader-step" class="mt-2 text-sm text-gray-500">@lang('Sending brief to the AI')</p>
        <div class="mt-5 flex items-center justify-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-brand-600 animate-bounce" class="delay-0"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-brand-600 animate-bounce" class="delay-150"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-brand-600 animate-bounce" class="delay-300"></span>
        </div>
        <p class="mt-5 text-xs text-gray-400">{{ __('This may take 15–60 seconds. Please don\'t close this window.') }}</p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';
    var form   = document.getElementById('template-form');
    var loader = document.getElementById('ai-loader');
    var step   = document.getElementById('ai-loader-step');

    var steps = [
        @json(__('Sending brief to the AI')),
        @json(__('Drafting contract clauses')),
        @json(__('Inserting smart placeholders')),
        @json(__('Building wizard fields')),
        @json(__('Finalizing template'))
    ];

    form.addEventListener('submit', function () {
        loader.classList.remove('hidden');
        loader.classList.add('flex');
        document.body.style.overflow = 'hidden';

        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'wait'; }

        var i = 0;
        setInterval(function () {
            i = (i + 1) % steps.length;
            step.textContent = steps[i];
        }, 3500);
    });
})();
</script>
@endpush
@endsection

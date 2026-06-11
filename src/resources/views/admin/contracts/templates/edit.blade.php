@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl space-y-5">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Edit Template')</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ $template->name }}</p>
        </div>
        <form id="regenerate-form" method="POST" action="{{ route('admin.contracts.templates.regenerate', $template) }}" data-no-page-loader>
            @csrf
            <button type="submit"
                    onclick="return confirm('@lang('Regenerate the body from the description? This will replace the current body and fields.')')"
                    class="inline-flex items-center gap-2 rounded-lg border border-brand-300 px-3 py-1.5 text-sm font-medium text-brand-700 hover:bg-brand-50">
                <x-heroicon-o-arrow-path class="h-4 w-4" />
                @lang('Regenerate with AI')
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.contracts.templates.update', $template) }}" class="space-y-5">
        @csrf @method('PUT')


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800">@lang('Template Details')</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Name') <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Category') <span class="text-red-500">*</span></label>
                    <select name="category_id" class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $template->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Credits Cost')</label>
                    <input type="number" name="credits_cost" value="{{ old('credits_cost', $template->credits_cost) }}" min="0"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Sort')</label>
                    <input type="number" name="sort" value="{{ old('sort', $template->sort) }}"
                           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" {{ old('active', $template->active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                        <span class="text-sm font-medium text-gray-700">@lang('Active')</span>
                    </label>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
            <div>
                <h2 class="font-semibold text-gray-800">@lang('Contract Description')</h2>
                <p class="text-xs text-gray-400 mt-0.5">@lang('Used when regenerating the body with AI.')</p>
            </div>
            <textarea name="description" rows="4"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('description', $template->description) }}</textarea>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
            <div>
                <h2 class="font-semibold text-gray-800">@lang('Contract Body') <span class="text-red-500">*</span></h2>
                <p class="text-xs text-gray-400 mt-0.5">@lang('Generated HTML with') <code class="bg-gray-100 px-1 rounded">&#123;&#123;placeholder&#125;&#125;</code> @lang('tokens. Edit freely — saving will resync wizard fields from the placeholders below.')</p>
            </div>
            <textarea name="body" rows="20" required
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs font-mono shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('body', $template->body) }}</textarea>
            @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5 space-y-3">
            <div>
                <h2 class="font-semibold text-gray-800">@lang('Wizard Fields')</h2>
                <p class="text-xs text-gray-400 mt-0.5">@lang('Auto-extracted from placeholders in the body. These are what the client fills in before generating their contract.')</p>
            </div>

            @if($template->fields->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4 border border-dashed border-gray-200 rounded-lg">
                    @lang('No placeholders detected. Add') <code class="bg-gray-100 px-1 rounded">&#123;&#123;placeholder&#125;&#125;</code> @lang('tokens in the body above.')
                </p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">@lang('Label')</th>
                                <th class="px-3 py-2 text-left font-medium">@lang('Placeholder')</th>
                                <th class="px-3 py-2 text-left font-medium">@lang('Type')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($template->fields as $field)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800">{{ $field->label }}</td>
                                    <td class="px-3 py-2 text-gray-500"><code class="text-xs">&#123;&#123;{{ $field->key }}&#125;&#125;</code></td>
                                    <td class="px-3 py-2 text-gray-500">{{ $field->type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                @lang('Save Changes')
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
        <h2 class="text-lg font-bold text-gray-900">@lang('Regenerating template…')</h2>
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
$(function () {
    'use strict';

    var $form   = $('#regenerate-form');
    var $loader = $('#ai-loader');
    var $step   = $('#ai-loader-step');

    if (!$form.length) return;

    var steps = [
        @json(__('Sending brief to the AI')),
        @json(__('Drafting contract clauses')),
        @json(__('Inserting smart placeholders')),
        @json(__('Building wizard fields')),
        @json(__('Finalizing template'))
    ];

    $form.on('submit', function () {
        $loader.removeClass('hidden').addClass('flex');
        $('body').css('overflow', 'hidden');

        var i = 0;
        setInterval(function () {
            i = (i + 1) % steps.length;
            $step.text(steps[i]);
        }, 3500);
    });
});
</script>
@endpush
@endsection

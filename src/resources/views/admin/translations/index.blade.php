@extends('admin.layouts.app')

@section('title', __('Translations'))

@section('content')
<div class="space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('admin.languages.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="page-title">@lang('Translations') — {{ $selectedLang?->name ?? strtoupper($locale) }}</h1>
            <p class="page-subtitle">@lang('Translating from') <b>{{ strtoupper($defaultLocale) }}</b> → <b>{{ strtoupper($locale) }}</b></p>
        </div>
    </div>


    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button type="button" data-modal-open="add-translation-modal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-plus class="h-4 w-4" />
                @lang('Add Key')
            </button>
            @if($locale !== $defaultLocale)
            <div class="flex items-center gap-2">
                <button type="button" id="sync-keywords-btn"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg id="sync-keywords-spinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-arrow-path id="sync-keywords-icon" class="h-4 w-4" />
                    <span id="sync-keywords-text">@lang('Sync Keywords')</span>
                </button>
                <button type="button" id="auto-translate-btn"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors whitespace-nowrap">
                    <svg id="auto-translate-spinner" class="hidden animate-spin h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-language id="auto-translate-icon" class="h-4 w-4 flex-shrink-0" />
                    <span id="auto-translate-text" class="min-w-max">@lang('Auto-translate')</span>
                </button>
                <span id="pending-count" class="text-xs text-gray-400">
                    <svg class="inline animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading…
                </span>
            </div>
            @endif
        </div>


        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search English key or value...') }}"
                   class="h-9 w-64 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">@lang('Search')</button>
            @if(request('q'))
                <a href="{{ route('admin.translations.index', ['locale' => $locale]) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>


    <x-modal id="add-translation-modal" :title="__('Add Translation Key')">
        <form method="POST" action="{{ route('admin.translations.store') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="hidden" name="group" value="*">
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">@lang('English Text')</label>
                    <input type="text" name="key" placeholder="{{ __('Welcome to our site') }}" required
                           class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ strtoupper($locale) }} @lang('Translation')</label>
                    <input type="text" name="value" placeholder="{{ __('Translation in :locale', ['locale' => $selectedLang?->name ?? $locale]) }}" required
                           class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" data-modal-close="add-translation-modal"
                        class="btn-secondary">
                    <x-heroicon-o-x-mark class="h-4 w-4" />
                    @lang('Cancel')
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    <x-heroicon-o-check class="h-4 w-4" />
                    @lang('Add')
                </button>
            </div>
        </form>
    </x-admin::modal>


    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide w-12"></th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide" class="col-w-half">@lang('English Text')</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide" class="col-w-half">
                        <span class="text-brand-600">{{ strtoupper($locale) }} @lang('Translation')</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($translations as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2 text-center" data-label="">
                        <form method="POST" action="{{ route('admin.translations.destroy', $t->key) }}" class="inline delete-translation-form">
                            @csrf @method('DELETE')
                            <button type="submit" data-confirm="{{ __('Delete \':key\' from all languages?', ['key' => $t->key]) }}" class="text-red-400 hover:text-red-600 text-sm">&times;</button>
                        </form>
                    </td>
                    <td class="px-5 py-2" :data-label="__('English Text')">
                        <div class="text-sm text-gray-900">{{ $t->key }}</div>
                    </td>
                    <td class="px-5 py-2" data-label="{{ strtoupper($locale) }} @lang('Translation')">
                        <form method="POST" action="{{ route('admin.translations.store') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $locale }}">
                            <input type="hidden" name="group" value="*">
                            <input type="hidden" name="key" value="{{ $t->key }}">
                            <input type="text"
                                   name="value"
                                   value="{{ $existingTranslations[$t->key] ?? '' }}"
                                   dir="{{ $langDirection ?? 'ltr' }}"
                                   class="flex-1 rounded border px-2 py-1 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500
                                          {{ !($existingTranslations[$t->key] ?? null) ? 'border-amber-300 bg-amber-50 placeholder-amber-400' : 'border-gray-200' }}"
                                   placeholder="{{ __('Enter translation...') }}">
                            <button type="submit" class="flex-shrink-0 rounded-lg bg-brand-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-brand-700">@lang('Save')</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-5 py-14 text-center text-sm text-gray-400">
                        @if($translations->total() === 0 && !request('q'))
                            <p class="mb-2">@lang('No translation keys yet.')</p>
                            <p>@lang('Add English keys using the form above. When you add new languages later, all keys will be copied automatically.')</p>
                        @else
                            @lang('No results match your search.') <a href="{{ route('admin.translations.index', ['locale' => $locale]) }}" class="text-brand-600 hover:underline">@lang('Clear search')</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($translations->hasPages())
        <div class="border-t border-gray-100 bg-gray-50/50 px-5 py-3">
            <x-admin::pagination :show-total="false" :paginator="$translations" />
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(function () {
    'use strict';

    var locale = '{{ $locale }}';
    var defaultLocale = '{{ $defaultLocale }}';
    var $pending = $('#pending-count');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    var showToast = function (message, type) {
        window.toast(message, type || 'info');
    };

    $('#sync-keywords-btn').on('click', function () {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var $spinner = $('#sync-keywords-spinner');
        var $icon = $('#sync-keywords-icon');
        var $text = $('#sync-keywords-text');

        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $icon.addClass('hidden');
        $text.text('Scanning…');

        $.ajax({
            url: '{{ route('admin.translations.sync-keywords') }}',
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        }).done(function (data) {
            var msg;
            if (data.added > 0) {
                msg = data.added + ' new keywords added (' + data.found + ' total).';
                setTimeout(function () {
                    window.location.reload();
                }, 1500);
                showToast(msg, 'success');
            } else {
                msg = '0 new keywords added (' + data.found + ' total).';
                showToast(msg, 'info');
                $btn.prop('disabled', false);
                $spinner.addClass('hidden');
                $icon.removeClass('hidden');
                $text.text('@lang('Sync Keywords')');
            }
        }).fail(function () {
            showToast('Failed to sync keywords.', 'error');
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $icon.removeClass('hidden');
            $text.text('@lang('Sync Keywords')');
        });
    });

    // Fetch pending count on load
    if ($pending.length && locale !== defaultLocale) {
        $.ajax({
            url: '{{ route('admin.translations.auto-translate') }}',
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            data: JSON.stringify({ locale: locale }),
        }).done(function (data) {
            var total = data.total || 0;
            $pending.text(total > 0 ? total + ' pending' : 'All translated');
            $pending.attr('class', total > 0 ? 'text-xs text-amber-600 font-medium' : 'text-xs text-green-600 font-medium');
        }).fail(function () {
            $pending.text('—');
        });
    }

    function resetBtn() {
        var $atBtn = $('#auto-translate-btn');
        $atBtn.prop('disabled', false);
        $('#auto-translate-spinner').addClass('hidden');
        $('#auto-translate-icon').removeClass('hidden');
        $('#auto-translate-text').text('@lang('Auto-translate')');
        $pending.removeClass('hidden');
    }

    function startAutoTranslate() {
        var $atBtn = $('#auto-translate-btn');
        var $spinner = $('#auto-translate-spinner');
        var $icon = $('#auto-translate-icon');
        var $text = $('#auto-translate-text');

        $atBtn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $icon.addClass('hidden');
        $pending.addClass('hidden');

        showToast('⏱️ Translation started. This may take several minutes due to Google\'s rate limiting (~1 second per key). Please wait...', 'info');

        var batchSize = 10;
        var totalProcessed = 0;
        var totalPending = 0;
        var errors = 0;
        var isFirstBatch = true;

        function fetchAndProcessNextBatch() {
            $.ajax({
                url: '{{ route('admin.translations.auto-translate') }}',
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                data: JSON.stringify({ locale: locale }),
            }).done(function (data) {
                if (data.error) {
                    showToast(data.error, 'error');
                    resetBtn();
                    return;
                }

                var pending = data.pending || [];
                var total = data.total || 0;

                if (isFirstBatch) {
                    totalPending = total;
                    isFirstBatch = false;
                }

                if (pending.length === 0 || totalProcessed >= totalPending) {
                    $text.text(totalPending + '/' + totalPending);
                    var msg = errors > 0
                        ? 'Done. ' + (totalProcessed - errors) + ' translated, ' + errors + ' failed.'
                        : 'Auto-translated ' + totalProcessed + ' keys to ' + locale.toUpperCase() + '.';
                    showToast(msg, errors > 0 ? 'warning' : 'success');
                    $pending.text('All translated');
                    $pending.attr('class', 'text-xs text-green-600 font-medium');
                    resetBtn();
                    return;
                }

                var batch = pending.slice(0, batchSize);

                var startProgress = totalProcessed;
                var endProgress = totalProcessed + batch.length;
                var duration = 3000;
                var startTime = Date.now();
                var animationFrame;

                var animateProgress = function () {
                    var elapsed = Date.now() - startTime;
                    var progress = Math.min(elapsed / duration, 1);
                    var currentProgress = Math.floor(startProgress + (endProgress - startProgress) * progress);
                    $text.text(currentProgress + '/' + totalPending);

                    if (progress < 1) {
                        animationFrame = requestAnimationFrame(animateProgress);
                    }
                };
                animateProgress();

                $.ajax({
                    url: '{{ route('admin.translations.auto-translate-batch') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    data: JSON.stringify({ locale: locale, batch: batch }),
                }).done(function () {
                    cancelAnimationFrame(animationFrame);
                    totalProcessed = endProgress;
                    $text.text(totalProcessed + '/' + totalPending);
                    var remaining = totalPending - totalProcessed;

                    if (remaining <= 0) {
                        $text.text(totalPending + '/' + totalPending);
                        $pending.text('All translated');
                        var msg = errors > 0
                            ? 'Done. ' + (totalPending - errors) + ' translated, ' + errors + ' failed.'
                            : 'Auto-translated ' + totalPending + ' keys to ' + locale.toUpperCase() + '.';
                        showToast(msg, errors > 0 ? 'warning' : 'success');
                        $pending.attr('class', 'text-xs text-green-600 font-medium');
                        resetBtn();
                    } else {
                        $pending.text(remaining + ' remaining');
                        setTimeout(fetchAndProcessNextBatch, 500);
                    }
                }).fail(function () {
                    errors += batch.length;
                    totalProcessed += batch.length;
                    setTimeout(fetchAndProcessNextBatch, 500);
                });
            }).fail(function () {
                showToast('Failed to fetch pending translations.', 'error');
                resetBtn();
            });
        }

        fetchAndProcessNextBatch();
    }

    $('#auto-translate-btn').on('click', function () {
        var title = '{{ __('Auto-translate all missing keys to :lang?', ['lang' => strtoupper($locale)]) }}';
        var message = '⏱️ This will take time due to Google\'s rate limits (~1 second per key). Do not close this page.';
        window.confirmModal.show(title, message, startAutoTranslate);
    });
});
</script>
@endpush
@endsection

@extends('admin.layouts.app')

@section('title', __('Translations'))

@section('content')
<div class="space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('admin.languages.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="page-title">{{ __('Translations') }} — {{ $selectedLang?->name ?? strtoupper($locale) }}</h1>
            <p class="page-subtitle">{{ __('Translating from') }} <b>{{ strtoupper($defaultLocale) }}</b> → <b>{{ strtoupper($locale) }}</b></p>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button type="button" data-modal-open="add-translation-modal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-plus class="h-4 w-4" />
                {{ __('Add Key') }}
            </button>
            @if($locale !== $defaultLocale)
            <div class="flex items-center gap-2">
                <button type="button" id="sync-keywords-btn" onclick="handleSyncKeywords(this); return false;"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg id="sync-keywords-spinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-arrow-path id="sync-keywords-icon" class="h-4 w-4" />
                    <span id="sync-keywords-text">{{ __('Sync Keywords') }}</span>
                </button>
                <button type="button" id="auto-translate-btn"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-medium text-brand-700 hover:bg-brand-100 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                    <svg id="auto-translate-spinner" class="hidden animate-spin h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-language id="auto-translate-icon" class="h-4 w-4 flex-shrink-0" />
                    <span id="auto-translate-text" class="min-w-max">{{ __('Auto-translate') }}</span>
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

        {{-- Filters --}}
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search English key or value...') }}"
                   class="h-9 w-64 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">{{ __('Search') }}</button>
            @if(request('q'))
                <a href="{{ route('admin.translations.index', ['locale' => $locale]) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    {{-- Add Translation Modal --}}
    <x-modal id="add-translation-modal" title="{{ __('Add Translation Key') }}">
        <form method="POST" action="{{ route('admin.translations.store') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="hidden" name="group" value="*">
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('English Text') }}</label>
                    <input type="text" name="key" placeholder="{{ __('Welcome to our site') }}" required
                           class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ strtoupper($locale) }} {{ __('Translation') }}</label>
                    <input type="text" name="value" placeholder="{{ __('Translation in :locale', ['locale' => $selectedLang?->name ?? $locale]) }}" required
                           class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" data-modal-close="add-translation-modal"
                        class="btn-secondary">
                    <x-heroicon-o-x-mark class="h-4 w-4" />
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    <x-heroicon-o-check class="h-4 w-4" />
                    {{ __('Add') }}
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Translations list --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide w-12"></th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide" class="col-w-half">{{ __('English Text') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide" class="col-w-half">
                        <span class="text-brand-600">{{ strtoupper($locale) }} {{ __('Translation') }}</span>
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
                    <td class="px-5 py-2" data-label="{{ __('English Text') }}">
                        <div class="text-sm text-gray-900">{{ $t->key }}</div>
                    </td>
                    <td class="px-5 py-2" data-label="{{ strtoupper($locale) }} {{ __('Translation') }}">
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
                            <button type="submit" class="flex-shrink-0 rounded-lg bg-brand-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-brand-700">{{ __('Save') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-5 py-14 text-center text-sm text-gray-400">
                        @if($translations->total() === 0 && !request('q'))
                            <p class="mb-2">{{ __('No translation keys yet.') }}</p>
                            <p>{{ __('Add English keys using the form above. When you add new languages later, all keys will be copied automatically.') }}</p>
                        @else
                            {{ __('No results match your search.') }} <a href="{{ route('admin.translations.index', ['locale' => $locale]) }}" class="text-brand-600 hover:underline">{{ __('Clear search') }}</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($translations->hasPages())
        <div class="border-t border-gray-100 bg-gray-50/50 px-5 py-3">
            <x-pagination :paginator="$translations" />
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const locale = '{{ $locale }}';
const defaultLocale = '{{ $defaultLocale }}';
const pendingEl = document.getElementById('pending-count');

function handleSyncKeywords(btn) {
    console.log('Sync button clicked');
    if (btn.disabled) return;
    const spinner = document.getElementById('sync-keywords-spinner');
    const icon = document.getElementById('sync-keywords-icon');
    const text = document.getElementById('sync-keywords-text');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = 'Scanning…';
    fetch('{{ route('admin.translations.sync-keywords') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        let msg;
        if (data.added > 0) {
            msg = 'Found ' + data.found + ' keys. Added ' + data.added + ' new keywords.';
            // Reload page only if new keywords were added
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            msg = 'All ' + data.found + ' keywords already synced.';
            showToast(msg, 'info');
            btn.disabled = false;
            spinner.classList.add('hidden');
            icon.classList.remove('hidden');
            text.textContent = '{{ __('Sync Keywords') }}';
        }
        if (data.added > 0) {
            showToast(msg, 'success');
        }
    })
    .catch(() => {
        showToast('Failed to sync keywords.', 'error');
        btn.disabled = false;
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        text.textContent = '{{ __('Sync Keywords') }}';
    });
}

// Fetch pending count on load
if (pendingEl && locale !== defaultLocale) {
    fetch('{{ route('admin.translations.auto-translate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ locale: locale }),
    })
    .then(r => r.json())
    .then(data => {
        const total = data.total || 0;
        pendingEl.textContent = total > 0 ? `${total} pending` : 'All translated';
        pendingEl.className = total > 0 ? 'text-xs text-amber-600 font-medium' : 'text-xs text-green-600 font-medium';
    })
    .catch(() => {
        pendingEl.textContent = '—';
    });
}

function startAutoTranslate() {
    const atBtn = document.getElementById('auto-translate-btn');
    const spinner = document.getElementById('auto-translate-spinner');
    const icon = document.getElementById('auto-translate-icon');
    const text = document.getElementById('auto-translate-text');

    atBtn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    pendingEl.classList.add('hidden');

    showToast('⏱️ Translation started. This may take several minutes due to Google\'s rate limiting (~1 second per key). Please wait...', 'info');

    const batchSize = 10;
    let totalProcessed = 0;
    let totalPending = 0;
    let batchCount = 0;
    let errors = 0;
    let isFirstBatch = true;

    function fetchAndProcessNextBatch() {
            // Fetch next batch of pending translations
            fetch('{{ route('admin.translations.auto-translate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ locale: locale }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    showToast(data.error, 'error');
                    resetBtn();
                    return;
                }

                const pending = data.pending || [];
                const total = data.total || 0;

                // On first batch, save the total
                if (isFirstBatch) {
                    totalPending = total;
                    isFirstBatch = false;
                }

                // If no pending keys or we've processed all, we're done
                if (pending.length === 0 || totalProcessed >= totalPending) {
                    text.textContent = `${totalPending}/${totalPending}`;
                    const msg = errors > 0
                        ? `Done. ${totalProcessed - errors} translated, ${errors} failed.`
                        : `Auto-translated ${totalProcessed} keys to ${locale.toUpperCase()}.`;
                    showToast(msg, errors > 0 ? 'warning' : 'success');
                    pendingEl.textContent = 'All translated';
                    pendingEl.className = 'text-xs text-green-600 font-medium';
                    resetBtn();
                    return;
                }

                // Take only first 10 from this batch
                const batch = pending.slice(0, batchSize);
                batchCount++;

                // Start animation immediately while batch translates on server
                const startProgress = totalProcessed;
                const endProgress = totalProcessed + batch.length;
                const duration = 3000; // 3 seconds to animate through the batch
                const startTime = Date.now();
                let animationFrame;

                const animateProgress = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const currentProgress = Math.floor(startProgress + (endProgress - startProgress) * progress);
                    text.textContent = `${currentProgress}/${totalPending}`;

                    if (progress < 1) {
                        animationFrame = requestAnimationFrame(animateProgress);
                    }
                };
                animateProgress();

                // Send batch for translation
                fetch('{{ route('admin.translations.auto-translate-batch') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ locale: locale, batch: batch }),
                })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(batchData => {
                    // Cancel animation and jump to exact count
                    cancelAnimationFrame(animationFrame);
                    totalProcessed = endProgress;
                    text.textContent = `${totalProcessed}/${totalPending}`;
                    const remaining = totalPending - totalProcessed;

                    if (remaining <= 0) {
                        text.textContent = `${totalPending}/${totalPending}`;
                        pendingEl.textContent = 'All translated';
                        const msg = errors > 0
                            ? `Done. ${totalPending - errors} translated, ${errors} failed.`
                            : `Auto-translated ${totalPending} keys to ${locale.toUpperCase()}.`;
                        showToast(msg, errors > 0 ? 'warning' : 'success');
                        pendingEl.className = 'text-xs text-green-600 font-medium';
                        resetBtn();
                    } else {
                        pendingEl.textContent = `${remaining} remaining`;
                        setTimeout(fetchAndProcessNextBatch, 500);
                    }
                })
                .catch(err => {
                    errors += batch.length;
                    totalProcessed += batch.length;

                    // Continue even on error
                    setTimeout(fetchAndProcessNextBatch, 500);
                });
            })
            .catch(() => {
                showToast('Failed to fetch pending translations.', 'error');
                resetBtn();
            });
        }

    // Start the process
    fetchAndProcessNextBatch();

    function resetBtn() {
        const atBtn = document.getElementById('auto-translate-btn');
        const spinner = document.getElementById('auto-translate-spinner');
        const icon = document.getElementById('auto-translate-icon');
        const text = document.getElementById('auto-translate-text');

        atBtn.disabled = false;
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        text.textContent = '{{ __('Auto-translate') }}';
        pendingEl.classList.remove('hidden');
    }
}

const atBtn = document.getElementById('auto-translate-btn');
if (atBtn) {
    atBtn.addEventListener('click', function () {
        const title = '{{ __('Auto-translate all missing keys to :lang?', ['lang' => strtoupper($locale)]) }}';
        const message = '⏱️ This will take time due to Google\'s rate limits (~1 second per key). Do not close this page.';
        window.confirmModal.show(title, message, startAutoTranslate);
    });
}

const showToast = (message, type = 'info') => window.toast(message, type);
</script>
@endpush
@endsection

@props(['admin' => false])

@php $isAdmin = $admin || !isset($activeTheme); @endphp

<div id="confirm-modal"
     class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog" aria-modal="true">

    <div class="modal-backdrop absolute inset-0 bg-black/50 transition-opacity duration-200" data-modal-close="confirm-modal"></div>

    <div id="confirm-panel"
         class="relative w-full max-w-sm rounded-2xl shadow-xl scale-95 transition-transform duration-200
                {{ $isAdmin ? 'bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700' : 'bg-[#161616] border border-white/10' }}">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-lg
                            {{ $isAdmin ? 'bg-yellow-100 dark:bg-yellow-950' : 'bg-primary/10' }}">
                    <x-heroicon-o-question-mark-circle class="h-6 w-6 {{ $isAdmin ? 'text-yellow-600 dark:text-yellow-400' : 'text-primary' }}" />
                </div>
                <h3 class="text-base font-semibold {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }}" id="confirm-title">Confirm action</h3>
            </div>
            <p class="text-sm mb-6 ml-13 {{ $isAdmin ? 'text-gray-600 dark:text-slate-300' : 'text-white/55' }}" id="confirm-message">Are you sure?</p>

            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ $isAdmin ? 'border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600' : 'border border-white/10 bg-white/5 text-white/70 hover:bg-white/10' }}"
                        onclick="window.confirmModal.close()">
                    @lang('Cancel')
                </button>
                <button type="button" id="confirm-btn"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ $isAdmin ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-primary text-black hover:bg-primary/90' }}">
                    @lang('Confirm')
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    'use strict';

    $('#confirm-btn').on('click', function () {
        if (window.confirmModal && window.confirmModal.onConfirm) {
            window.confirmModal.onConfirm();
        }
        if (window.confirmModal) {
            window.confirmModal.close();
        }
    });
});
</script>

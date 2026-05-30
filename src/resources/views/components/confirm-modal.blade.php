<div id="confirm-modal"
     class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog" aria-modal="true">

    <div class="modal-backdrop absolute inset-0 bg-black/50 transition-opacity duration-200" data-modal-close="confirm-modal"></div>


    @php $isDarkTheme = str_contains(request()->getRequestUri(), '') && isset($activeTheme); @endphp
    <div id="confirm-panel"
         class="relative w-full max-w-sm rounded-2xl shadow-xl scale-95 transition-transform duration-200
                {{ isset($activeTheme) ? 'bg-[#161616] border border-white/10' : 'bg-white dark:bg-slate-900' }}">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-lg
                            {{ isset($activeTheme) ? 'bg-lime/10' : 'bg-yellow-100 dark:bg-yellow-950' }}">
                    <x-heroicon-o-question-mark-circle class="h-6 w-6 {{ isset($activeTheme) ? 'text-lime' : 'text-yellow-600 dark:text-yellow-400' }}" />
                </div>
                <h3 class="text-base font-semibold {{ isset($activeTheme) ? 'text-white' : 'text-gray-900 dark:text-white' }}" id="confirm-title">Confirm action</h3>
            </div>
            <p class="text-sm mb-6 ml-13 {{ isset($activeTheme) ? 'text-white/55' : 'text-gray-600 dark:text-slate-300' }}" id="confirm-message">Are you sure?</p>

            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ isset($activeTheme) ? 'border border-white/10 bg-white/5 text-white/70 hover:bg-white/10' : 'border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                        onclick="window.confirmModal.close()">
                    {{ __('Cancel') }}
                </button>
                <button type="button" id="confirm-btn"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ isset($activeTheme) ? 'bg-lime text-black hover:bg-lime/90' : 'bg-red-600 text-white hover:bg-red-700' }}">
                    {{ __('Confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const confirmBtn = document.getElementById('confirm-btn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (window.confirmModal && window.confirmModal.onConfirm) {
                window.confirmModal.onConfirm();
            }
            if (window.confirmModal) {
                window.confirmModal.close();
            }
        });
    }
});
</script>

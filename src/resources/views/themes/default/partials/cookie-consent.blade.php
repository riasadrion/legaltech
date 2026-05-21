@if(setting('controls.cookie_consent') === '1')
<div id="cookie-banner"
     class="fixed bottom-0 left-0 right-0 z-50 px-4 pb-4 sm:bottom-4 sm:left-auto sm:right-4 sm:max-w-sm"
     style="display:none;">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-xl p-5">
        <div class="flex items-start gap-3 mb-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 12c0 6.627 5.373 12 12 12 6.628 0 12-5.373 12-12 0-1.993-.487-3.867-1.349-5.521"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ __('We use cookies') }}</p>
                <p class="mt-0.5 text-xs text-gray-500 leading-relaxed">
                    {{ __('We use cookies to improve your experience. By continuing, you agree to our') }}
                    <a href="/cookie-policy" class="underline text-teal-600 hover:text-teal-700">{{ __('Cookie Policy') }}</a>.
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <button id="cookie-accept"
                class="flex-1 rounded-lg bg-teal-600 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                {{ __('Accept All') }}
            </button>
            <button id="cookie-decline"
                class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                {{ __('Decline') }}
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var STORAGE_KEY = 'cookie_consent';
    var banner = document.getElementById('cookie-banner');
    if (!banner) return;

    if (!localStorage.getItem(STORAGE_KEY)) {
        setTimeout(function () { banner.style.display = ''; }, 600);
    }

    function dismiss(value) {
        localStorage.setItem(STORAGE_KEY, value);
        banner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(12px)';
        setTimeout(function () { banner.style.display = 'none'; }, 320);
    }

    document.getElementById('cookie-accept').addEventListener('click', function () { dismiss('accepted'); });
    document.getElementById('cookie-decline').addEventListener('click', function () { dismiss('declined'); });
})();
</script>
@endif

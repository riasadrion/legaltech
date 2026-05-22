{{-- Frontend preloader — pen writing animation + progress bar at top --}}
<div id="fe-preloader">
    <div id="fe-preloader-bar"></div>
    <div id="fe-preloader-writer">
        <div id="fe-preloader-line"></div>
        <div id="fe-preloader-pen-wrap">
            <svg id="fe-preloader-pen" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g transform="rotate(-45 2 48)">
                    {{-- Nib: teardrop/spear shape, classic fountain pen nib --}}
                    <path d="M2 48 L5 46.5 L11 47 L11 49 L5 49.5 Z"
                          fill="#D4FF3A" stroke="#0B0B0F" stroke-width="0.4" stroke-linejoin="round"/>

                    {{-- Center slit (the iconic line down the middle of a nib) --}}
                    <line x1="3" y1="48" x2="10" y2="48" stroke="#0B0B0F" stroke-width="0.5" stroke-linecap="round"/>

                    {{-- Breather hole --}}
                    <circle cx="9" cy="48" r="0.7" fill="#0B0B0F"/>

                    {{-- Grip section --}}
                    <path d="M11 47 L14 46.5 L14 49.5 L11 49 Z"
                          fill="rgba(212,255,58,0.4)" stroke="#D4FF3A" stroke-width="0.6"/>

                    {{-- Decorative metal ring at grip/barrel join --}}
                    <rect x="14" y="46.3" width="1.6" height="3.4" fill="#D4FF3A"/>

                    {{-- Barrel --}}
                    <path d="M15.6 46.5 L38 46.5 L38 49.5 L15.6 49.5 Z"
                          fill="rgba(212,255,58,0.18)" stroke="#D4FF3A" stroke-width="0.9"/>

                    {{-- Cap band --}}
                    <rect x="38" y="46.1" width="1.8" height="3.8" fill="#D4FF3A"/>

                    {{-- Cap body --}}
                    <path d="M39.8 46.1 L46 46.1 L46 49.9 L39.8 49.9 Z"
                          fill="rgba(212,255,58,0.3)" stroke="#D4FF3A" stroke-width="0.9"/>

                    {{-- Cap clip --}}
                    <path d="M41 45 L41 46.1 L43.5 46.1 L43.5 45 Z" fill="#D4FF3A"/>

                    {{-- Rounded finial --}}
                    <path d="M46 46.1 Q47.8 48 46 49.9 Z" fill="#D4FF3A"/>
                </g>
            </svg>
        </div>
    </div>
    <div id="fe-preloader-label">{{ __('Loading') }}</div>
</div>

@once
@push('scripts')
<script>
(function () {
    var bar = document.getElementById('fe-preloader-bar');
    var loader = document.getElementById('fe-preloader');
    if (!bar || !loader) return;

    var progress = 0;
    var interval = setInterval(function () {
        progress += Math.random() * 18;
        if (progress > 85) progress = 85;
        bar.style.width = progress + '%';
    }, 120);

    function finish() {
        clearInterval(interval);
        bar.style.width = '100%';
        setTimeout(function () {
            loader.classList.add('fe-preloader-done');
        }, 300);
    }

    if (document.readyState === 'complete') {
        finish();
    } else {
        window.addEventListener('load', finish);
        setTimeout(finish, 4000);
    }
})();
</script>
@endpush
@endonce

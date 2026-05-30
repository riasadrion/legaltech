
<div id="fe-preloader">
    <div id="fe-preloader-writer">
        <div id="fe-preloader-line"></div>
        <div id="fe-preloader-pen-wrap">
            <svg id="fe-preloader-pen" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <style>
                    #fe-preloader-pen .pen-solid { fill: var(--brand-color); }
                    #fe-preloader-pen .pen-dim   { fill: color-mix(in srgb, var(--brand-color) 30%, transparent); stroke: var(--brand-color); }
                    #fe-preloader-pen .pen-mid   { fill: color-mix(in srgb, var(--brand-color) 18%, transparent); stroke: var(--brand-color); }
                </style>
                <g transform="rotate(-45 2 48)">

                    <path d="M2 48 L5 46.5 L11 47 L11 49 L5 49.5 Z"
                          class="pen-solid" stroke="#0B0B0F" stroke-width="0.4" stroke-linejoin="round"/>
                    <line x1="3" y1="48" x2="10" y2="48" stroke="#0B0B0F" stroke-width="0.5" stroke-linecap="round"/>
                    <circle cx="9" cy="48" r="0.7" fill="#0B0B0F"/>

                    <path d="M11 47 L14 46.5 L14 49.5 L11 49 Z" class="pen-dim" stroke-width="0.6"/>

                    <rect x="14" y="46.3" width="1.6" height="3.4" class="pen-solid"/>

                    <path d="M15.6 46.5 L38 46.5 L38 49.5 L15.6 49.5 Z" class="pen-mid" stroke-width="0.9"/>

                    <rect x="38" y="46.1" width="1.8" height="3.8" class="pen-solid"/>

                    <path d="M39.8 46.1 L46 46.1 L46 49.9 L39.8 49.9 Z" class="pen-dim" stroke-width="0.9"/>

                    <path d="M41 45 L41 46.1 L43.5 46.1 L43.5 45 Z" class="pen-solid"/>

                    <path d="M46 46.1 Q47.8 48 46 49.9 Z" class="pen-solid"/>
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
    'use strict';
    var loader = document.getElementById('fe-preloader');
    if (!loader) return;

    function finish() {
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

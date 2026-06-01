@if(setting('controls.cookie_consent') === '1')
{{-- ───────────────── GDPR Cookie Consent ─────────────────
     - Banner: bottom-left dark glass card
     - Stores consent in localStorage as JSON with version + timestamp
     - Re-openable via floating "Cookie settings" pill
     - All styles in default.css (no inline styles)
─────────────────────────────────────────────────────── --}}


<div id="cookie-banner" class="cc-banner" role="dialog" aria-live="polite" :aria-label="__('Cookie consent')">
    <div class="cc-card">
        <button type="button" id="cookie-reject" class="cc-banner-close" :aria-label="__('Reject non-essential cookies')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="cc-card-head">
            <div class="cc-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11a3 3 0 0 1-3-3 1 1 0 0 0-1-1 3 3 0 0 1-2.84-3.94 1 1 0 0 0-.21-1A10 10 0 1 0 22 12a1.06 1.06 0 0 0-.402-.936zM12 20a8 8 0 0 1-7.992-8.39 8 8 0 0 1 7.6-7.6 5 5 0 0 0 4.486 5.91A5 5 0 0 0 21 14.31 8 8 0 0 1 12 20z"/>
                    <circle cx="12" cy="14" r="1"/>
                    <circle cx="16" cy="17" r="1"/>
                    <circle cx="8" cy="12" r="1"/>
                    <circle cx="11" cy="9" r="1"/>
                </svg>
            </div>
            <div class="cc-card-body">
                <p class="cc-title">@lang('We use cookies')</p>
                <p class="cc-desc">
                    @lang('We use strictly necessary cookies to make this site work — session, security, and language preference. We never set marketing or tracking cookies without your consent.')
                    <a href="{{ route('page.show', 'cookie-policy') }}" class="cc-link">@lang('Learn more')</a>
                </p>
            </div>
        </div>

        <div class="cc-actions">
            <button type="button" id="cookie-accept" class="cc-btn cc-btn-primary">
                @lang('Accept')
            </button>
            <button type="button" id="cookie-prefs" class="cc-btn cc-btn-ghost">
                @lang('Preferences')
            </button>
        </div>
    </div>
</div>


<div id="cookie-prefs-modal" class="cc-modal" role="dialog" aria-modal="true" aria-labelledby="cc-prefs-title">
    <div class="cc-modal-backdrop" data-cc-close></div>
    <div class="cc-modal-panel">
        <div class="cc-modal-head">
            <h3 id="cc-prefs-title" class="cc-modal-title">@lang('Cookie preferences')</h3>
            <button type="button" class="cc-modal-close" data-cc-close :aria-label="__('Close')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <p class="cc-modal-intro">
            @lang('We respect your privacy. Choose what you want to allow. You can change this anytime via the cookie settings link in the footer.')
        </p>

        <ul class="cc-cat-list">
            <li class="cc-cat">
                <div class="cc-cat-head">
                    <div>
                        <p class="cc-cat-title">@lang('Strictly necessary')</p>
                        <p class="cc-cat-desc">@lang('Required for the site to function: session, CSRF protection, language. Cannot be disabled.')</p>
                    </div>
                    <span class="cc-pill cc-pill-locked">@lang('Always on')</span>
                </div>
            </li>
        </ul>

        <div class="cc-modal-actions">
            <button type="button" class="cc-btn cc-btn-ghost" data-cc-close>@lang('Cancel')</button>
            <button type="button" id="cookie-save-prefs" class="cc-btn cc-btn-primary">@lang('Save preferences')</button>
        </div>
    </div>
</div>


<button type="button" id="cookie-reopen" class="cc-reopen" :aria-label="__('Cookie settings')">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11a3 3 0 0 1-3-3 1 1 0 0 0-1-1 3 3 0 0 1-2.84-3.94 1 1 0 0 0-.21-1A10 10 0 1 0 22 12a1.06 1.06 0 0 0-.402-.936z"/>
    </svg>
    @lang('Cookies')
</button>

@push('scripts')
<script>
(function () {
    'use strict';

    var STORAGE_KEY = 'cc_consent_v1';
    var banner = document.getElementById('cookie-banner');
    var modal = document.getElementById('cookie-prefs-modal');
    var reopen = document.getElementById('cookie-reopen');
    if (!banner || !modal || !reopen) return;

    function readConsent() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch (e) { return null; }
    }

    function writeConsent(payload) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    }

    function show(el)  { el.classList.add('cc-open');  }
    function hide(el)  { el.classList.remove('cc-open'); }

    var SNOOZE_DAYS = 7;

    function showBanner()    { show(banner);  hide(reopen); }
    function hideBanner()    { hide(banner);  show(reopen); }
    function hideAll()       { hide(banner);  hide(reopen); }
    function openPrefs()     { show(modal); }
    function closePrefs()    { hide(modal); }

    function isSnoozed() {
        try {
            var s = JSON.parse(localStorage.getItem(STORAGE_KEY + '_snooze'));
            return s && s.until && new Date(s.until) > new Date();
        } catch (e) { return false; }
    }

    function snooze() {
        var until = new Date();
        until.setDate(until.getDate() + SNOOZE_DAYS);
        localStorage.setItem(STORAGE_KEY + '_snooze', JSON.stringify({ until: until.toISOString() }));
    }

    function setConsent(decision) {
        writeConsent({
            version: 1,
            timestamp: new Date().toISOString(),
            decision: decision,
            categories: { necessary: true }
        });
        hideAll();
        closePrefs();
    }

    // Initial state
    var saved = readConsent();
    if (saved && saved.decision) {
        hideAll();
    } else if (isSnoozed()) {
        hideAll();
    } else {
        setTimeout(function () { show(banner); }, 600);
    }

    // Wire buttons
    document.getElementById('cookie-accept').addEventListener('click', function () { setConsent('accepted'); });
    document.getElementById('cookie-reject').addEventListener('click', function () { snooze(); hideAll(); closePrefs(); });
    document.getElementById('cookie-prefs').addEventListener('click', openPrefs);
    document.getElementById('cookie-save-prefs').addEventListener('click', function () { setConsent('saved'); });
    reopen.addEventListener('click', function () { showBanner(); });

    // Close modal via backdrop / X / Cancel
    modal.querySelectorAll('[data-cc-close]').forEach(function (el) {
        el.addEventListener('click', closePrefs);
    });

    // Escape to close modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('cc-open')) closePrefs();
    });
})();
</script>
@endpush
@endif

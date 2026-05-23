'use strict';

import Sortable from 'sortablejs';
import { confirmModal, toast } from './utils/ui';

const $ = window.jQuery;
window.Sortable = Sortable;
window.confirmModal = confirmModal;
window.toast = toast;

// Page loader
(function () {
    const overlay  = document.getElementById('page-loader');
    const ring     = document.getElementById('loader-ring');
    const countEl  = document.getElementById('loader-count');
    if (!overlay || !ring || !countEl) return;

    const CIRCUMFERENCE = 238.8;
    let raf, current = 0, target = 0, navigating = false;

    function setProgress(pct) {
        const offset = CIRCUMFERENCE - (pct / 100) * CIRCUMFERENCE;
        ring.style.strokeDashoffset = offset;
        countEl.textContent = Math.round(pct) + '%';
    }

    function animate() {
        if (current < target) {
            current = Math.min(current + 1.2, target);
            setProgress(current);
        }
        if (current < 100) {
            raf = requestAnimationFrame(animate);
        } else {
            setTimeout(hide, 200);
        }
    }

    function show() {
        current = 0;
        target = 85;
        setProgress(0);
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const loaderCircle = overlay.querySelector('div');
        if (loaderCircle) {
            loaderCircle.style.background = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.55)';
        }
        overlay.style.display = 'flex';
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(animate);
    }

    function finish() {
        target = 100;
        if (!raf) raf = requestAnimationFrame(animate);
    }

    function hide() {
        overlay.style.display = 'none';
        current = 0;
        target = 0;
        navigating = false;
    }

    document.addEventListener('click', function (e) {
        const a = e.target.closest('a[href]');
        if (!a) return;
        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank') return;
        navigating = true;
        show();
    });

    document.addEventListener('submit', function (e) {
        if (e.target && e.target.hasAttribute && e.target.hasAttribute('data-no-page-loader')) return;
        navigating = true;
        show();
    });

    window.addEventListener('pageshow', function () {
        if (navigating) finish();
    });
})();

$(document).ready(function () {
    'use strict';

    // Sidebar toggle (mobile)
    $(document).on('click', '#sidebar-toggle, #sidebar-overlay', function () {
        $('#admin-sidebar').toggleClass('hidden flex flex-col fixed inset-y-0 start-0 z-30 w-64');
        $('#sidebar-overlay').toggleClass('hidden');
    });

    // Active nav highlight
    const currentPath = window.location.pathname;
    $('#admin-sidebar a[data-route]').each(function () {
        if ($(this).data('route') && currentPath.startsWith($(this).data('route'))) {
            $(this).addClass('bg-brand-50 text-brand-700 font-medium');
        }
    });

    // Submenu expand
    $(document).on('click', '[data-submenu-toggle]', function () {
        const target = $($(this).data('submenu-toggle'));
        target.toggleClass('hidden');
        $(this).find('[data-chevron]').toggleClass('rotate-180');
    });

    // Confirm dialogs - use modal instead of alert()
    $(document).on('click', '[data-confirm]', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const message = $(this).data('confirm');
        const title = $(this).data('confirm-title') || 'Confirm action';

        window.confirmModal.show(title, message, () => {
            form[0].submit();
        });
    });

    // Modal: open / close
    $(document).on('click', '[data-modal-open]', function () {
        const id = $(this).data('modal-open');
        $('#' + id).removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    $(document).on('click', '[data-modal-close]', function () {
        const id = $(this).data('modal-close');
        $('#' + id).addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('.modal:not(.hidden)').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }
    });

    // Backdrop click closes modal
    $(document).on('click', '.modal-backdrop', function () {
        const id = $(this).data('modal-close');
        $('#' + id).addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Translatable input: locale pills
    $(document).on('click', '.locale-tab', function () {
        const locale = $(this).data('locale');

        $(this).closest('.translatable-input').find('.locale-tab')
            .removeClass('bg-gray-100 text-gray-900')
            .addClass('text-gray-400');
        $(this).addClass('bg-gray-100 text-gray-900')
               .removeClass('text-gray-400');

        $(this).closest('.translatable-input').find('.locale-panel').addClass('hidden');
        $(this).closest('.translatable-input')
               .find('.locale-panel[data-locale="' + locale + '"]').removeClass('hidden');
    });

    // Signal that jQuery is ready for inline page scripts
    document.dispatchEvent(new CustomEvent('admin:ready'));
});

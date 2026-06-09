'use strict';

import Sortable from 'sortablejs';
import { confirmModal, toast } from './utils/ui';

const $ = window.jQuery;
window.Sortable = Sortable;
window.confirmModal = confirmModal;
window.toast = toast;

// Page loader (top progress bar)
(function () {
    const overlay = document.getElementById('page-loader');
    const bar     = document.getElementById('loader-bar');
    if (!overlay || !bar) return;

    let timer, current = 0, navigating = false;

    function setProgress(pct) {
        current = pct;
        bar.style.width = pct + '%';
    }

    function trickle() {
        if (current < 90) {
            const step = current < 50 ? 8 : (current < 75 ? 3 : 1);
            setProgress(Math.min(current + step, 90));
        }
        timer = setTimeout(trickle, 300);
    }

    function show() {
        clearTimeout(timer);
        overlay.style.display = 'block';
        setProgress(0);
        bar.offsetWidth; // force reflow so transition animates from 0
        setProgress(15);
        timer = setTimeout(trickle, 300);
    }

    function finish() {
        clearTimeout(timer);
        setProgress(100);
        setTimeout(hide, 250);
    }

    function hide() {
        overlay.style.display = 'none';
        bar.style.transition = 'none';
        setProgress(0);
        bar.offsetWidth;
        bar.style.transition = '';
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

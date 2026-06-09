'use strict';

import Sortable from 'sortablejs';
import { ImageResizer } from './utils/image-resize';
import { confirmModal, toast } from './utils/ui';
import { initReveal } from './utils/reveal';

initReveal();

const $ = window.jQuery;
window.Sortable = Sortable;
window.ImageResizer = ImageResizer;
window.confirmModal = confirmModal;
window.toast = toast;

$(document).ready(function () {
    'use strict';

    // Flash message: auto-dismiss + close button
    window.setTimeout(function () {
        $('.flash-message').fadeOut(600);
    }, 4000);

    $(document).on('click', '.flash-close', function () {
        $(this).closest('.flash-message').fadeOut(300);
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

    // Tabs (data-tab-group + data-tab)
    $(document).on('click', '[data-tab]', function () {
        const group  = $(this).data('tab-group');
        const target = $(this).data('tab');

        $('[data-tab-group="' + group + '"]')
            .removeClass('border-brand-600 text-brand-700')
            .addClass('border-transparent text-gray-500');
        $(this).addClass('border-brand-600 text-brand-700')
               .removeClass('border-transparent text-gray-500');

        $('[data-tab-panel-group="' + group + '"]').addClass('hidden');
        $('[data-tab-panel="' + target + '"][data-tab-panel-group="' + group + '"]').removeClass('hidden');
    });

    // Translatable input: locale tabs
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

    // Sortable lists
    $('.sortable-list').each(function () {
        const el     = this;
        const url    = $(el).data('sortable-url');
        const handle = $(el).data('sortable-handle') || null;

        Sortable.create(el, {
            handle:    handle,
            animation: 150,
            onEnd: function () {
                if (!url) return;
                const ids = [];
                $(el).children('[data-id]').each(function () {
                    ids.push($(this).data('id'));
                });
                App.ajax({ method: 'POST', url: url, data: { order: ids } });
            },
        });
    });

    // Auto-submit forms (data-auto-submit)
    $(document).on('change', '[data-auto-submit]', function () {
        $(this).closest('form').trigger('submit');
    });

    // Confirm dialogs (data-confirm)
    $(document).on('click', '[data-confirm]', function (e) {
        e.preventDefault();
        const $el = $(this);
        const message = $el.data('confirm');
        const title = $el.data('confirm-title') || 'Confirm action';
        const form = $el.closest('form');

        window.confirmModal.show(title, message, () => {
            if (form.length) {
                form[0].submit();
            } else if ($el.is('a')) {
                window.location.href = $el.attr('href');
            }
        });
    });

    // Notification dropdown
    $(document).on('click', '[data-notification-trigger]', function () {
        const $dropdown = $(this).closest('[data-notification-dropdown]');
        const $panel = $dropdown.find('.notification-panel');
        $panel.toggleClass('hidden');
    });

    $(document).on('click', function (e) {
        const $dropdown = $('[data-notification-dropdown]');
        if (!$dropdown.has(e.target).length && !$(e.target).closest('[data-notification-dropdown]').length) {
            $dropdown.find('.notification-panel').addClass('hidden');
        }
    });

    // Usage bars
    $('.usage-bar-fill[data-pct]').each(function () {
        $(this).css('width', $(this).data('pct') + '%');
    });

    // Locale switcher
    $(document).on('click', '[data-locale-switch]', function () {
        const $btn = $(this);
        if ($btn.prop('disabled')) return;

        $btn.prop('disabled', true);

        App.ajax({
            method: 'POST',
            url: $btn.data('locale-url'),
        }).done(function () {
            window.location.reload();
        }).fail(function () {
            $btn.prop('disabled', false);
        });
    });
});

// Global AJAX helper — auto-injects CSRF + X-Requested-With
window.App = window.App || {};

window.App.ajax = function (options) {
    'use strict';

    return $.ajax($.extend({
        headers: {
            'X-CSRF-TOKEN':     $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
    }, options));
};

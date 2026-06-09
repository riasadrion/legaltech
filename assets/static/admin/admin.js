/* global jQuery, toast */
(function ($) {
    'use strict';

    if (!$) { return; }

    // -----------------------------------------------------------------------
    // 1. Theme (light/dark) — runs BEFORE document.ready to avoid FOUC
    // -----------------------------------------------------------------------
    (function initTheme() {
        var saved = localStorage.getItem('admin-theme') || 'dark';
        $('html').attr('data-theme', saved);
        $(function () {
            $('#theme-icon-sun').toggleClass('hidden', saved !== 'dark');
            $('#theme-icon-moon').toggleClass('hidden', saved !== 'light');
        });
    }());

    // -----------------------------------------------------------------------
    // 2. onAdminReady — queue for page scripts that depend on layout init
    // -----------------------------------------------------------------------
    window._adminReady = window._adminReady || [];
    var adminReadyFired = false;
    $(document).on('admin:ready', function () {
        adminReadyFired = true;
        $.each(window._adminReady, function (_, fn) { fn(); });
        window._adminReady = [];
    });
    window.onAdminReady = function (fn) {
        if (adminReadyFired) { fn(); } else { window._adminReady.push(fn); }
    };

    // -----------------------------------------------------------------------
    // 3. DOM-ready: wire all the things
    // -----------------------------------------------------------------------
    $(function () {
        'use strict';
        // --- Sidebar mobile overlay (replaces inline onclick) ---
        $('#sidebarOverlay').on('click', function () {
            $('#adminSidebar').removeClass('open');
            $(this).removeClass('show');
        });

        // --- Menu search (cmd-k style) ---
        var $searchInput = $('#menu-search');
        if ($searchInput.length) {
            var $searchResults = $('#search-results');
            var $searchContainer = $('#search-container');
            var menuItems = [];

            $('#adminSidebar .sidebar-link').each(function () {
                var text = $.trim($(this).text());
                var href = $(this).attr('href');
                if (text && href) { menuItems.push({ text: text, href: href }); }
            });

            var $settingsData = $('#settings-tabs-data');
            if ($settingsData.length && $settingsData.data('tabs')) {
                try {
                    var tabs = $settingsData.data('tabs');
                    if (typeof tabs === 'string') { tabs = JSON.parse(tabs); }
                    $.each(tabs, function (_, t) {
                        menuItems.push({ text: t.name, href: t.link });
                    });
                } catch (e) { /* ignore */ }
            }

            $searchInput.on('input', function () {
                var query = $.trim($(this).val().toLowerCase());
                if (!query) { $searchResults.addClass('hidden'); return; }

                var filtered = $.grep(menuItems, function (i) {
                    return i.text.toLowerCase().indexOf(query) !== -1;
                });

                if (!filtered.length) {
                    $searchResults.html('<div class="p-4 text-sm text-gray-500 text-center">No results</div>');
                } else {
                    var html = $.map(filtered, function (item, idx) {
                        var border = idx < filtered.length - 1 ? ' border-b border-gray-100' : '';
                        return '<a href="' + item.href + '" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50' + border + '">' + $('<div>').text(item.text).html() + '</a>';
                    }).join('');
                    $searchResults.html(html);
                }
                $searchResults.removeClass('hidden');
            });

            $(document).on('click', function (e) {
                if (!$searchContainer.is(e.target) && !$searchContainer.has(e.target).length) {
                    $searchResults.addClass('hidden');
                }
            });

            $searchInput.on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $searchResults.addClass('hidden');
                    $(this).val('');
                }
            });
        }

        // --- Admin user dropdown ---
        var $userBtn = $('#admin-user-btn');
        var $userDropdown = $('#admin-user-dropdown');
        var $userChevron = $('#admin-user-chevron');
        if ($userBtn.length && $userDropdown.length) {
            $userBtn.on('click', function (e) {
                e.stopPropagation();
                $userDropdown.toggleClass('open');
                $userChevron.css('transform', $userDropdown.hasClass('open') ? 'rotate(180deg)' : 'rotate(0)');
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#admin-user-menu').length) {
                    $userDropdown.removeClass('open');
                    $userChevron.css('transform', 'rotate(0)');
                }
            });
        }

        // --- Language switcher dropdown ---
        var $langBtn = $('#admin-lang-btn');
        var $langDropdown = $('#admin-lang-dropdown');
        if ($langBtn.length && $langDropdown.length) {
            $langBtn.on('click', function (e) {
                e.stopPropagation();
                $langDropdown.toggleClass('open');
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#admin-lang-menu').length) {
                    $langDropdown.removeClass('open');
                }
            });
        }

        // --- Notifications bell dropdown ---
        var $notifBtn = $('#notifications-btn');
        var $notifDropdown = $('#notifications-dropdown');
        var $notifDot = $('#notif-dot');
        var $notifList = $('#notif-list');
        var $notifEmpty = $('#notif-empty');
        var notifEndpoint = $notifBtn.data('endpoint');

        function loadNotifications() {
            if (!notifEndpoint) { return; }
            $.getJSON(notifEndpoint).done(function (data) {
                if ($notifDot.length) { $notifDot.toggleClass('show', data.unread > 0); }
                if (!data.items || !data.items.length) { return; }
                $notifEmpty.addClass('hidden');
                $notifList.find('.notif-item').remove();

                $.each(data.items, function (_, n) {
                    var titleHtml = n.url
                        ? '<a href="' + n.url + '" class="notif-title block truncate hover:text-brand-600' + (n.unread ? ' font-semibold' : '') + '">' + $('<div>').text(n.title).html() + '</a>'
                        : '<p class="notif-title truncate' + (n.unread ? ' font-semibold' : '') + '">' + $('<div>').text(n.title).html() + '</p>';

                    var bodyHtml = n.body ? '<p class="text-xs text-gray-500 mt-0.5 line-clamp-2">' + $('<div>').text(n.body).html() + '</p>' : '';

                    var $el = $(
                        '<div class="notif-item flex gap-3 px-4 py-3 cursor-pointer' + (n.unread ? ' unread' : '') + '">' +
                            '<div class="mt-1.5 h-2 w-2 shrink-0 rounded-full ' + (n.unread ? 'bg-brand-500' : 'bg-gray-200') + '"></div>' +
                            '<div class="flex-1 min-w-0">' +
                                titleHtml +
                                bodyHtml +
                                '<p class="text-xs text-gray-400 mt-1">' + $('<div>').text(n.time).html() + '</p>' +
                            '</div>' +
                        '</div>'
                    );

                    if (n.url) {
                        $el.on('click', function () { window.location.href = n.url; });
                    }
                    $notifList.append($el);
                });
            });
        }

        if ($notifBtn.length && $notifDropdown.length) {
            loadNotifications();

            $notifBtn.on('click', function (e) {
                e.stopPropagation();
                var opening = !$notifDropdown.hasClass('open');
                $notifDropdown.toggleClass('open');
                if (opening) { loadNotifications(); }
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#notifications-menu').length) {
                    $notifDropdown.removeClass('open');
                }
            });
        }

        // --- Theme toggle button ---
        var $themeToggle = $('#theme-toggle');
        if ($themeToggle.length) {
            $themeToggle.on('click', function () {
                var $html = $('html');
                var isDark = $html.attr('data-theme') === 'dark';
                var next = isDark ? 'light' : 'dark';
                $html.attr('data-theme', next);
                localStorage.setItem('admin-theme', next);
                $('#theme-icon-sun').toggleClass('hidden', isDark);
                $('#theme-icon-moon').toggleClass('hidden', !isDark);
            });
        }

        // --- Sidebar collapse / mobile toggle ---
        var $sidebar = $('#adminSidebar');
        var $toggleBtn = $('#sidebar-toggle');
        var $overlay = $('#sidebarOverlay');
        if ($sidebar.length && $toggleBtn.length) {
            if (localStorage.getItem('sidebar-collapsed') === '1') {
                $sidebar.addClass('collapsed');
            }
            updateSidebarTooltips();
            $toggleBtn.on('click', function () {
                if ($(window).width() <= 768) {
                    $sidebar.toggleClass('open');
                    $overlay.toggleClass('show');
                } else {
                    $sidebar.toggleClass('collapsed');
                    localStorage.setItem('sidebar-collapsed', $sidebar.hasClass('collapsed') ? '1' : '0');
                    updateSidebarTooltips();
                }
            });
        }

        function updateSidebarTooltips() {
            $sidebar.find('.sidebar-link').each(function () {
                var $link = $(this);
                if ($sidebar.hasClass('collapsed')) {
                    $link.attr('title', $link.data('label') || '');
                } else {
                    $link.removeAttr('title');
                }
            });
        }

        // --- Clear cache (admin tool) ---
        $('#clear-cache-btn').on('click', function () {
            var $btn = $(this);
            var endpoint = $btn.data('endpoint');
            if (!endpoint) { return; }

            $btn.prop('disabled', true);
            $.ajax({
                url: endpoint,
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            })
            .done(function (data) {
                if (data && data.success) {
                    if (window.toast) { window.toast('Cache cleared successfully.', 'success'); }
                    setTimeout(function () { window.location.reload(); }, 800);
                } else if (window.toast) {
                    window.toast((data && data.message) || 'Failed to clear cache.', 'error');
                }
            })
            .fail(function () {
                if (window.toast) { window.toast('Failed to clear cache.', 'error'); }
            })
            .always(function () {
                $btn.prop('disabled', false);
            });
        });

        // --- Responsive tables: auto-populate data-label from <th> headers ---
        $('table.responsive-table').each(function () {
            var headers = [];
            $(this).find('thead th').each(function () {
                var t = $.trim($(this).text());
                if (t) { headers.push(t); }
            });
            $(this).find('tbody tr').each(function () {
                $(this).find('td').each(function (i) {
                    if (!$(this).attr('data-label') && headers[i]) {
                        $(this).attr('data-label', headers[i]);
                    }
                });
            });
        });

        // --- Scroll active sidebar link into view on page load ---
        var $active = $('.sidebar-nav .sidebar-link.active').first();
        if ($active.length && $active[0].scrollIntoView) {
            $active[0].scrollIntoView({ block: 'center', behavior: 'instant' });
        }

        // --- Generic auto-submit ([data-auto-submit] on filter selects) ---
        $(document).on('change', '[data-auto-submit]', function () {
            $(this).closest('form').trigger('submit');
        });

        // --- Password show/hide (used on login + reset views) ---
        // Markup contract: a button with data-toggle-password="<input-id>" and a span#eyeIcon-<input-id> SVG inner.
        $(document).on('click', '[data-toggle-password]', function () {
            var targetId = $(this).data('toggle-password');
            var $input = $('#' + targetId);
            var $icon = $('#eyeIcon-' + targetId).length ? $('#eyeIcon-' + targetId) : $(this).find('[data-eye-icon]');
            var isText = $input.attr('type') === 'text';
            $input.attr('type', isText ? 'password' : 'text');
            if ($icon.length) {
                var eyeOff = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
                var eyeOn = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                $icon.html(isText ? eyeOn : eyeOff);
            }
        });

        // --- Mobile: dual horizontal scrollbar above wide tables ---
        if ($(window).width() < 640) {
            $('.admin-content .overflow-x-auto').each(function () {
                var $wrap = $(this);
                var $inner = $wrap.find('table').first();
                if (!$inner.length) { return; }

                var $top = $('<div class="table-top-scroll" />');
                var $spacer = $('<div />');
                $top.append($spacer);
                $wrap.before($top);

                function syncWidth() { $spacer.css('width', $inner[0].scrollWidth + 'px'); }
                syncWidth();

                $top.on('scroll', function () { $wrap.scrollLeft($top.scrollLeft()); });
                $wrap.on('scroll', function () { $top.scrollLeft($wrap.scrollLeft()); });
            });
        }

    }); // end document.ready

}(window.jQuery));

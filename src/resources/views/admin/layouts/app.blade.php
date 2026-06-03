<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? '') ? $title . ' — ' : '' }}{{ setting('site.name', config('app.name')) }} Admin</title>
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ upload_url($favicon) }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/admin.js']); ?>
    @stack('head')
</head>
<body class="antialiased text-gray-900">


<div id="page-loader">
    <div id="loader-bar"></div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="document.getElementById('adminSidebar').classList.remove('open');this.classList.remove('show')"></div>

<div class="admin-shell">


    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand hover:opacity-75 transition-opacity">
            @php
                $siteName = setting('site.name', config('app.name'));
                $siteLogo = setting('site.logo');
                $siteDarkLogo = setting('site.dark_logo');
            @endphp
            @if($siteLogo || $siteDarkLogo)

                @if($siteLogo)
                    <img src="{{ upload_url($siteLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-dark">
                @endif
                @if($siteDarkLogo)
                    <img src="{{ upload_url($siteDarkLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-light">
                @elseif($siteLogo)
                    <img src="{{ upload_url($siteLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-light">
                @endif
            @else
                <div class="sidebar-brand-icon">{{ strtoupper(substr($siteName, 0, 1)) }}</div>
                <span class="sidebar-brand-text">{{ $siteName }}</span>
            @endif
        </a>

        <div class="sidebar-nav">
            @php $navItems = \App\Support\AdminNavigation::forUser(auth('admin')->user()); @endphp
            @foreach ($navItems as $item)
                @if (empty($item['children']))
                    @php
                        $isActive = false;
                        if (isset($item['route'])) {
                            $parts = explode('.', $item['route']);
                            $isActive = count($parts) <= 2
                                ? request()->routeIs($item['route'])
                                : request()->routeIs(\Illuminate\Support\Str::beforeLast($item['route'], '.') . '.*');
                        }
                    @endphp
                    <div class="sidebar-section">
                        <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#' }}"
                           class="sidebar-link {{ $isActive ? 'active' : '' }}"
                           data-label="{{ $item['label'] }}">
                            @if (!empty($item['icon']))
                                <x-dynamic-component :component="$item['icon']" class="w-4 h-4 flex-shrink-0 sidebar-link-icon" />
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </div>
                @else
                    @php
                        $submenuId = 'submenu-' . $item['key'];
                        $isParentActive = false;
                        foreach($item['children'] as $child) {
                            if (isset($child['route'])) {
                                $parts = explode('.', $child['route']);
                                $match = count($parts) <= 2
                                    ? request()->routeIs($child['route'])
                                    : request()->routeIs(\Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*');
                                if ($match) { $isParentActive = true; break; }
                            }
                        }
                    @endphp
                    <div class="sidebar-section">
                        <div class="sidebar-section-label">{{ $item['label'] }}</div>
                        @foreach ($item['children'] as $child)
                            @php
                                $isChildActive = false;
                                if (isset($child['route'])) {
                                    $parts = explode('.', $child['route']);
                                    $isChildActive = count($parts) <= 2
                                        ? request()->routeIs($child['route'])
                                        : request()->routeIs(\Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*');
                                }
                            @endphp
                            <a href="{{ isset($child['route']) && Route::has($child['route']) ? route($child['route']) : '#' }}"
                               class="sidebar-link {{ $isChildActive ? 'active' : '' }}"
                               data-label="{{ $child['label'] }}">
                                @if (!empty($child['icon']))
                                    <x-dynamic-component :component="$child['icon']" class="w-4 h-4 flex-shrink-0 sidebar-link-icon" />
                                @endif
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>

        <div class="sidebar-user">
            <div class="sidebar-user-inner">
                <div class="sidebar-user-avatar">{{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}</div>
                <div>
                    <div class="sidebar-user-name">{{ auth('admin')->user()?->name }}</div>
                    <div class="sidebar-user-role">Admin</div>
                </div>
            </div>
        </div>
    </aside>


    <div class="admin-main" id="adminMainContent">


        <header class="admin-header">
            <button class="header-btn sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
                <x-heroicon-o-bars-3 class="h-5 w-5" />
            </button>


            <div class="relative flex-1 max-w-md" id="search-container">
                <div class="header-search">
                    <x-heroicon-o-magnifying-glass class="h-4 w-4 text-gray-400 flex-shrink-0" />
                    <input type="text" id="menu-search" :placeholder="__('Search pages...')">
                </div>
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto"></div>
            </div>


            <div id="settings-tabs-data" data-tabs="{{ json_encode([
                ['name' => 'General Settings', 'link' => route('admin.settings.index').'#general'],
                ['name' => 'Global Controls', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'Brand & Appearance', 'link' => route('admin.settings.index').'#brand'],
                ['name' => 'SEO & Footer', 'link' => route('admin.settings.index').'#seo'],
                ['name' => 'Integrations', 'link' => route('admin.settings.index').'#integrations'],
                ['name' => 'Email Settings', 'link' => route('admin.settings.index').'#email'],
                ['name' => 'SMS Settings', 'link' => route('admin.settings.index').'#sms'],
                ['name' => 'Storage Settings', 'link' => route('admin.settings.index').'#storage'],
                ['name' => 'Queue Settings', 'link' => route('admin.settings.index').'#queue'],

                // Global Controls items
                ['name' => 'User Registration', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'Email Verification', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'SMS Verification', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'KYC Verification', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'Policy Agreement (Terms & Privacy)', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'Maintenance Mode', 'link' => route('admin.settings.index').'#controls'],
                ['name' => 'Force HTTPS', 'link' => route('admin.settings.index').'#controls'],

                // Integrations items
                ['name' => 'Cloudflare Turnstile (Captcha)', 'link' => route('admin.settings.index').'#integrations'],
                ['name' => 'Google Analytics 4 (GA4)', 'link' => route('admin.settings.index').'#integrations'],
                ['name' => 'Tawk.to Live Chat', 'link' => route('admin.settings.index').'#integrations'],
            ]) }}"></div>

            <div class="header-actions">

                <div class="relative" id="notifications-menu">
                    <button type="button" id="notifications-btn" class="header-btn" title="Notifications">
                        <x-heroicon-o-bell class="h-5 w-5" />
                        <span class="dot" id="notif-dot"></span>
                    </button>
                    <div id="notifications-dropdown" class="notif-panel">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-semibold">Notifications</h3>
                            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-brand-600 hover:text-brand-700">View all</a>
                        </div>
                        <div id="notif-list" class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                            <div class="p-6 text-sm text-gray-400 text-center" id="notif-empty">
                                <x-heroicon-o-bell-slash class="h-7 w-7 text-gray-300 mx-auto mb-2" />
                                No notifications
                            </div>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.notifications.read-all') }}" id="notif-read-all-form">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Mark all as read</button>
                            </form>
                        </div>
                    </div>
                </div>


                <button type="button" id="theme-toggle" class="header-btn" title="Toggle theme">
                    <x-heroicon-o-sun class="h-5 w-5" id="theme-icon-sun" />
                    <x-heroicon-o-moon class="h-5 w-5 hidden" id="theme-icon-moon" />
                </button>


                <button type="button" id="clear-cache-btn" class="header-btn" title="Clear cache">
                    <x-heroicon-o-arrow-path class="h-5 w-5" />
                </button>


                <a href="{{ url('/') }}" target="_blank" class="header-btn" title="Visit site">
                    <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5" />
                </a>

                @php
                    $adminNavLanguages = \App\Models\Language::where('is_active', true)->orderBy('name')->get();
                    $adminCurrentLocale = app()->getLocale();
                    $adminCurrentLang = $currentLanguage ?? $adminNavLanguages->firstWhere('code', $adminCurrentLocale);
                @endphp
                @if($adminNavLanguages->count() > 1)
                <div class="relative" id="admin-lang-menu">
                    <button type="button" id="admin-lang-btn" title="Language" class="header-profile">
                        <x-heroicon-o-language class="h-4 w-4 header-profile-chevron" />
                        <span class="header-profile-name">{{ strtoupper($adminCurrentLang?->code ?? $adminCurrentLocale) }}</span>
                        <x-heroicon-o-chevron-down class="h-3 w-3 header-profile-chevron" />
                    </button>
                    <div id="admin-lang-dropdown" class="dropdown-panel">
                        @foreach($adminNavLanguages as $lang)
                        <form method="POST" action="{{ route('locale.set', $lang->code) }}">
                            @csrf
                            <button type="submit" class="w-full text-left dd-item">
                                @if($lang->code === $adminCurrentLocale)
                                    <x-heroicon-s-check class="h-3.5 w-3.5 text-brand-600 shrink-0" />
                                @else
                                    <span class="h-3.5 w-3.5 shrink-0"></span>
                                @endif
                                {{ $lang->name }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="header-sep"></div>


                <div class="relative" id="admin-user-menu">
                    <div class="header-profile" id="admin-user-btn">
                        <div class="header-profile-avatar">{{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}</div>
                        <span class="header-profile-name">{{ auth('admin')->user()?->name }}</span>
                        <x-heroicon-o-chevron-down class="h-3 w-3 header-profile-chevron" id="admin-user-chevron" />
                    </div>
                    <div id="admin-user-dropdown" class="dropdown-panel">
                        <div class="dd-header">
                            <div class="dd-name">{{ auth('admin')->user()?->name }}</div>
                            <div class="dd-email">{{ auth('admin')->user()?->email }}</div>
                        </div>
                        <a href="{{ route('admin.profile') }}" class="dd-item">
                            <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                            @lang('Profile')
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="dd-item">
                            <x-heroicon-o-cog-6-tooth class="h-4 w-4 text-gray-400" />
                            @lang('Settings')
                        </a>
                        <div class="dd-sep"></div>
                        <form method="POST" action="{{ route('admin.logout') }}" id="admin-logout-form">
                            @csrf
                            <button type="submit" class="dd-item danger w-full text-left">
                                <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4" />
                                @lang('Sign out')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>


        <x-flash />


        <main class="admin-content">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>

<script>
// Theme — default dark
(function () {
    'use strict';
    var saved = localStorage.getItem('admin-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
    document.getElementById('theme-icon-sun').classList.toggle('hidden', saved !== 'dark');
    document.getElementById('theme-icon-moon').classList.toggle('hidden', saved !== 'light');
})();

document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    const searchInput = document.getElementById('menu-search');
    const searchResults = document.getElementById('search-results');
    const searchContainer = document.getElementById('search-container');
    const adminSidebar = document.getElementById('adminSidebar');

    if (searchInput) {
        const menuItems = [];
        adminSidebar.querySelectorAll('.sidebar-link').forEach(link => {
            const text = link.textContent.trim();
            const href = link.href;
            if (text && href) menuItems.push({ text, href });
        });

        const settingsData = document.getElementById('settings-tabs-data');
        if (settingsData && settingsData.dataset.tabs) {
            try {
                JSON.parse(settingsData.dataset.tabs).forEach(tab => {
                    menuItems.push({ text: tab.name, href: tab.link });
                });
            } catch (e) {}
        }

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            if (!query) { searchResults.classList.add('hidden'); return; }

            const filtered = menuItems.filter(i => i.text.toLowerCase().includes(query));
            searchResults.innerHTML = filtered.length === 0
                ? '<div class="p-4 text-sm text-gray-500 text-center">No results</div>'
                : filtered.map((item, idx) => `<a href="${item.href}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 ${idx < filtered.length - 1 ? 'border-b border-gray-100' : ''}">${item.text}</a>`).join('');

            searchResults.classList.remove('hidden');
        });

        document.addEventListener('click', e => { if (!searchContainer.contains(e.target)) searchResults.classList.add('hidden'); });
        searchInput.addEventListener('keydown', e => { if (e.key === 'Escape') { searchResults.classList.add('hidden'); searchInput.value = ''; } });
    }

    // Admin user dropdown
    const userBtn = document.getElementById('admin-user-btn');
    const userDropdown = document.getElementById('admin-user-dropdown');
    const userChevron = document.getElementById('admin-user-chevron');
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
            userChevron.style.transform = userDropdown.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#admin-user-menu')) { userDropdown.classList.remove('open'); userChevron.style.transform = 'rotate(0)'; }
        });
    }

    // Language switcher dropdown
    const langBtn = document.getElementById('admin-lang-btn');
    const langDropdown = document.getElementById('admin-lang-dropdown');
    if (langBtn && langDropdown) {
        langBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            langDropdown.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#admin-lang-menu')) langDropdown.classList.remove('open');
        });
    }

    // Notifications — bell dropdown
    const notifBtn = document.getElementById('notifications-btn');
    const notifDropdown = document.getElementById('notifications-dropdown');
    const notifDot = document.getElementById('notif-dot');
    const notifList = document.getElementById('notif-list');
    const notifEmpty = document.getElementById('notif-empty');

    function loadNotifications() {
        fetch('{{ route("admin.notifications.recent") }}')
            .then(r => r.json())
            .then(function (data) {
                if (notifDot) notifDot.classList.toggle('show', data.unread > 0);
                if (!data.items.length) return;
                notifEmpty.classList.add('hidden');
                notifList.querySelectorAll('.notif-item').forEach(function (el) { el.remove(); });
                data.items.forEach(function (n) {
                    var el = document.createElement('div');
                    el.className = 'notif-item flex gap-3 px-4 py-3 cursor-pointer' + (n.unread ? ' unread' : '');
                    var titleTag = n.url
                        ? '<a href="' + n.url + '" class="notif-title block truncate hover:text-brand-600' + (n.unread ? ' font-semibold' : '') + '">' + n.title + '</a>'
                        : '<p class="notif-title truncate' + (n.unread ? ' font-semibold' : '') + '">' + n.title + '</p>';
                    el.innerHTML =
                        '<div class="mt-1.5 h-2 w-2 shrink-0 rounded-full ' + (n.unread ? 'bg-brand-500' : 'bg-gray-200') + '"></div>' +
                        '<div class="flex-1 min-w-0">' +
                            titleTag +
                            (n.body ? '<p class="text-xs text-gray-500 mt-0.5 line-clamp-2">' + n.body + '</p>' : '') +
                            '<p class="text-xs text-gray-400 mt-1">' + n.time + '</p>' +
                        '</div>';
                    if (n.url) {
                        el.addEventListener('click', function () { window.location.href = n.url; });
                    }
                    notifList.appendChild(el);
                });
            })
            .catch(function () {});
    }

    if (notifBtn && notifDropdown) {
        loadNotifications();

        notifBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var opening = !notifDropdown.classList.contains('open');
            notifDropdown.classList.toggle('open');
            if (opening) loadNotifications();
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#notifications-menu')) notifDropdown.classList.remove('open');
        });
    }

    // Theme toggle
    var themeToggle = document.getElementById('theme-toggle');
    var iconSun = document.getElementById('theme-icon-sun');
    var iconMoon = document.getElementById('theme-icon-moon');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var html = document.documentElement;
            var isDark = html.getAttribute('data-theme') === 'dark';
            html.setAttribute('data-theme', isDark ? 'light' : 'dark');
            localStorage.setItem('admin-theme', isDark ? 'light' : 'dark');
            iconSun.classList.toggle('hidden', isDark);
            iconMoon.classList.toggle('hidden', !isDark);
        });
    }

    // Sidebar toggle (mobile: open/close, desktop: collapse/expand)
    var sidebar = document.getElementById('adminSidebar');
    var toggleBtn = document.getElementById('sidebar-toggle');
    var overlay = document.getElementById('sidebarOverlay');
    if (sidebar && toggleBtn) {
        if (localStorage.getItem('sidebar-collapsed') === '1') {
            sidebar.classList.add('collapsed');
        }
        // Add/remove title tooltips on collapse
        function updateSidebarTooltips() {
            sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
                if (sidebar.classList.contains('collapsed')) {
                    link.title = link.getAttribute('data-label') || '';
                } else {
                    link.removeAttribute('title');
                }
            });
        }
        updateSidebarTooltips();
        toggleBtn.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
                updateSidebarTooltips();
            }
        });
    }

    // Clear cache
    const clearCacheBtn = document.getElementById('clear-cache-btn');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            fetch('{{ route('admin.cache.clear') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    toast('Cache cleared successfully.', 'success');
                    setTimeout(function () { window.location.reload(); }, 800);
                } else {
                    toast(data.message || 'Failed to clear cache.', 'error');
                }
            })
            .catch(function () { toast('Failed to clear cache.', 'error'); })
            .finally(function () { btn.disabled = false; });
        });
    }
});

// onAdminReady pattern for jQuery-backed page scripts
window._adminReady = window._adminReady || [];
(function () {
    'use strict';
    var fired = false;
    document.addEventListener('admin:ready', function () {
        fired = true;
        window._adminReady.forEach(function (fn) { fn(); });
        window._adminReady = [];
    });
    window.onAdminReady = function (fn) { if (fired) fn(); else window._adminReady.push(fn); };
}());

// Auto-set data-labels on responsive tables from headers
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    document.querySelectorAll('table.responsive-table').forEach(function (table) {
        var headers = [];
        table.querySelectorAll('thead th').forEach(function (th) {
            var t = th.textContent.trim();
            if (t) headers.push(t);
        });
        table.querySelectorAll('tbody tr').forEach(function (tr) {
            tr.querySelectorAll('td').forEach(function (td, i) {
                if (!td.hasAttribute('data-label') && headers[i]) {
                    td.setAttribute('data-label', headers[i]);
                }
            });
        });
    });
});
</script>

<x-confirm-modal :admin="true" />

<script>
(function () {
    'use strict';
    var active = document.querySelector('.sidebar-nav .sidebar-link.active');
    if (active) {
        active.scrollIntoView({ block: 'center', behavior: 'instant' });
    }
})();
</script>

<script>
if (window.innerWidth < 640) {
    document.querySelectorAll('.admin-content .overflow-x-auto').forEach(function (wrap) {
        var inner = wrap.querySelector('table');
        if (!inner) return;

        var top = document.createElement('div');
        top.className = 'table-top-scroll';
        top.style.cssText = 'overflow-x:auto;height:6px;margin-bottom:8px;border-radius:4px;';
        var spacer = document.createElement('div');
        top.appendChild(spacer);
        wrap.parentNode.insertBefore(top, wrap);

        function syncWidth() { spacer.style.width = inner.scrollWidth + 'px'; }
        syncWidth();

        top.addEventListener('scroll', function () { wrap.scrollLeft = top.scrollLeft; });
        wrap.addEventListener('scroll', function () { top.scrollLeft = wrap.scrollLeft; });
    });
}
</script>

@stack('scripts')
</body>
</html>
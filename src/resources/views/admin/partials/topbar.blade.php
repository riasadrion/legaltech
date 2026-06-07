<header class="admin-header">
    <button class="header-btn sidebar-toggle-mobile" id="sidebar-toggle-mobile" title="Toggle sidebar">
        <x-heroicon-o-bars-3 class="h-5 w-5" />
    </button>

    {{-- Global search --}}
    <div class="relative flex-1 max-w-md" id="search-container">
        <div class="header-search">
            <x-heroicon-o-magnifying-glass class="h-4 w-4 text-gray-400 flex-shrink-0" />
            <input type="text" id="menu-search" placeholder="{{ __('Search settings, tools...') }}">
        </div>
        <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto"></div>
    </div>

    {{-- Settings tabs index for search --}}
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

        {{-- Notifications bell --}}
        <div class="relative" id="notifications-menu">
            <button type="button" id="notifications-btn" class="header-btn" title="Notifications" data-endpoint="{{ route('admin.notifications.recent') }}">
                <x-heroicon-o-bell class="h-5 w-5" />
                <span class="dot" id="notif-dot"></span>
            </button>
            <div id="notifications-dropdown" class="notif-panel">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <p class="text-sm font-semibold">Notifications</p>
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

        {{-- Theme toggle --}}
        <button type="button" id="theme-toggle" class="header-btn hide-mobile-theme" title="Toggle theme">
            <x-heroicon-o-sun class="h-5 w-5" id="theme-icon-sun" />
            <x-heroicon-o-moon class="h-5 w-5 hidden" id="theme-icon-moon" />
        </button>

        {{-- Clear cache --}}
        <button type="button" id="clear-cache-btn" class="header-btn" title="Clear cache" data-endpoint="{{ route('admin.cache.clear') }}">
            <x-heroicon-o-arrow-path class="h-5 w-5" />
        </button>

        {{-- Visit site --}}
        <a href="{{ url('/') }}" target="_blank" class="header-btn" title="Visit site">
            <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5" />
        </a>

        {{-- Language switcher --}}
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

        {{-- User dropdown --}}
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

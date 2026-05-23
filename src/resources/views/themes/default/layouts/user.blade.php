<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @if(!empty($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ $favicon }}" type="image/png">
    @endif

    {{-- external-allowed: google-fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    {{-- external-allowed: google-fonts --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php $siteFont = setting('site.font', 'system'); @endphp
    {{-- external-allowed: google-fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600{{ $siteFont !== 'system' ? '&family=' . urlencode($siteFont) . ':wght@300;400;500;600;700;800' : '' }}&display=swap" rel="stylesheet">
    @if($siteFont !== 'system')
    <style>.theme-default { --font-frontend: '{{ $siteFont }}', ui-sans-serif, system-ui, sans-serif; }</style>
    @endif

    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
    @php $themeCssPath = base_path('../assets/static/css/themes/default.css'); @endphp
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}?v={{ is_file($themeCssPath) ? filemtime($themeCssPath) : 1 }}">
    <x-integrations.ga4 />
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased">

    <x-preloader />

    {{-- Flash messages --}}
    <x-flash />

    <div class="min-h-screen flex overflow-x-hidden" data-user-shell>

        {{-- SIDEBAR --}}
        <aside class="hidden md:flex md:flex-col w-64 shrink-0 border-r border-white/8 bg-ink-900 fixed inset-y-0 left-0 z-20" id="user-sidebar">
            @include('theme::partials.user-sidebar-compact')
        </aside>
        {{-- sidebar spacer so main content doesn't go under the fixed sidebar --}}
        <div class="hidden md:block w-64 shrink-0"></div>

        {{-- Mobile sidebar overlay --}}
        <div class="md:hidden fixed inset-0 bg-black/60 z-40 hidden" id="user-sidebar-overlay"></div>
        <aside class="md:hidden fixed inset-y-0 left-0 w-72 z-50 border-r border-white/8 bg-ink-900 -translate-x-full transition-transform flex flex-col" id="user-sidebar-mobile">
            @include('theme::partials.user-sidebar-compact')
        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0 flex flex-col overflow-x-hidden">

            {{-- topbar --}}
            <header class="sticky top-0 z-30 bg-ink-900/80 backdrop-blur border-b border-white/8">
                <div class="relative flex items-center gap-3 px-6 h-[64px]">
                    {{-- mobile burger --}}
                    <button type="button" id="user-sidebar-toggle"
                            class="md:hidden w-9 h-9 grid place-items-center rounded-lg border border-white/10 text-white/70">
                        <x-heroicon-o-bars-3 class="h-5 w-5" />
                    </button>

                    {{-- breadcrumb --}}
                    <div class="hidden md:flex items-center gap-2 text-[13px] text-white/55 shrink-0">
                        <x-heroicon-o-home class="h-4 w-4" />
                        <span class="text-white/55">{{ setting('site.name', config('app.name')) }}</span>
                        @hasSection('breadcrumb')
                            <x-heroicon-o-chevron-right class="h-3.5 w-3.5 text-white/30" />
                            <span class="text-white capitalize">@yield('breadcrumb')</span>
                        @endif
                    </div>

                    <div class="flex-1"></div>

                    {{-- search --}}
                    <div class="w-full max-w-xs md:max-w-sm hidden sm:block shrink-0">
                        <div class="flex items-center gap-2 rounded-xl bg-white/4 border border-white/10 px-3 py-2">
                            <x-heroicon-o-magnifying-glass class="h-4 w-4 text-white/40 shrink-0" />
                            <span class="flex-1 text-[13px] text-white/35 select-none">{{ __('Search…') }}</span>
                            <kbd class="hidden md:inline text-[10px] font-mono text-white/35 border border-white/10 rounded px-1.5 py-0.5">⌘K</kbd>
                        </div>
                    </div>

                    <div class="flex-1"></div>

                    {{-- right cluster --}}
                    @auth('user')
                    @php
                        $unreadCount = 0;
                        try { $unreadCount = auth('user')->user()->notifications()->whereNull('read_at')->count(); } catch(\Throwable) {}
                    @endphp
                    <div class="flex items-center gap-2 shrink-0">
                        {{-- new contract button --}}
                        <a href="{{ route('user.contracts.create') }}" class="hidden sm:flex btn-lime rounded-lg px-3 py-2 text-sm items-center gap-1.5">
                            <x-heroicon-o-sparkles class="h-4 w-4" />
                            {{ __('New contract') }}
                        </a>

                        {{-- notification bell --}}
                        <a href="{{ route('user.notifications.index') }}"
                           class="w-9 h-9 grid place-items-center rounded-lg bg-white/4 border border-white/10 text-white/70 relative hover:bg-white/8 transition"
                           aria-label="{{ __('Notifications') }}">
                            <x-heroicon-o-bell class="h-5 w-5" />
                            @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 grid place-items-center text-[9px] font-bold rounded-full bg-lime text-ink-900">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </a>

                        {{-- user dropdown --}}
                        <div class="relative" data-ud-dropdown>
                            <button type="button"
                                    class="flex items-center gap-2 rounded-lg pl-1 pr-2 py-1 bg-white/4 border border-white/10 hover:bg-white/8 transition"
                                    data-ud-dropdown-trigger aria-haspopup="true" aria-expanded="false">
                                @if(auth('user')->user()->avatar_path)
                                    <img src="{{ upload_url(auth('user')->user()->avatar_path) }}"
                                         alt="{{ auth('user')->user()->name }}"
                                         class="w-7 h-7 rounded-md object-cover">
                                @else
                                    <span class="w-7 h-7 rounded-md bg-gradient-to-br from-lime to-lime-deep grid place-items-center font-bold text-[11px] text-ink-900">
                                        {{ mb_strtoupper(mb_substr(auth('user')->user()->name ?? auth('user')->user()->email, 0, 1)) }}
                                    </span>
                                @endif
                                <span class="text-[12px] hidden md:inline text-white">{{ auth('user')->user()->name ?? auth('user')->user()->email }}</span>
                                <x-heroicon-o-chevron-down class="h-3.5 w-3.5 text-white/45" />
                            </button>

                            {{-- dropdown panel --}}
                            <div class="ud-user-dropdown hidden" data-ud-dropdown-panel>
                                <div class="ud-user-dropdown-header">
                                    <div class="text-[13px] font-medium text-white truncate">{{ auth('user')->user()->name ?? __('Member') }}</div>
                                    <div class="text-[11px] text-white/40 truncate mt-0.5">{{ auth('user')->user()->email }}</div>
                                </div>
                                <div class="ud-user-dropdown-body">
                                    <a href="{{ route('user.profile') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-user class="h-4 w-4" />{{ __('Profile') }}
                                    </a>
                                    <a href="{{ route('user.2fa.index') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-shield-check class="h-4 w-4" />{{ __('Security') }}
                                    </a>
                                    <a href="{{ route('user.notifications.index') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-bell class="h-4 w-4" />{{ __('Notifications') }}
                                        @if($unreadCount > 0)
                                        <span class="ml-auto pill pill-sign">{{ $unreadCount }}</span>
                                        @endif
                                    </a>
                                </div>
                                <div class="ud-user-dropdown-footer">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="ud-user-dropdown-item text-red-400 hover:text-red-300 w-full">
                                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />{{ __('Sign out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            {{-- scroll area --}}
            <main class="flex-1 overflow-y-auto">
                <div class="px-6 py-7 w-full">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Global Confirm Modal --}}
    <x-confirm-modal />

    <script src="{{ asset('assets/static/js/phone-input.js') }}"></script>
    @stack('scripts')
    <x-integrations.tawk />

    <script>
    $(function () {
        'use strict';
        // Mobile sidebar
        $(document).on('click', '#user-sidebar-toggle', function () {
            $('#user-sidebar-mobile').toggleClass('-translate-x-full');
            $('#user-sidebar-overlay').toggleClass('hidden');
        });
        $(document).on('click', '#user-sidebar-overlay', function () {
            $('#user-sidebar-mobile').addClass('-translate-x-full');
            $('#user-sidebar-overlay').addClass('hidden');
        });
        // User dropdown
        $(document).on('click', '[data-ud-dropdown-trigger]', function (e) {
            e.stopPropagation();
            var $wrap = $(this).closest('[data-ud-dropdown]');
            var $panel = $wrap.find('[data-ud-dropdown-panel]');
            var isOpen = !$panel.hasClass('hidden');
            // close all others
            $('[data-ud-dropdown-panel]').addClass('hidden');
            $('[data-ud-dropdown-trigger]').attr('aria-expanded', 'false');
            if (!isOpen) {
                $panel.removeClass('hidden');
                $(this).attr('aria-expanded', 'true');
            }
        });
        $(document).on('click', function () {
            $('[data-ud-dropdown-panel]').addClass('hidden');
            $('[data-ud-dropdown-trigger]').attr('aria-expanded', 'false');
        });
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                $('[data-ud-dropdown-panel]').addClass('hidden');
                $('[data-ud-dropdown-trigger]').attr('aria-expanded', 'false');
            }
        });
    });
    </script>
</body>
</html>

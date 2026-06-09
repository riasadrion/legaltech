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
    <link rel="icon" href="{{ upload_url($favicon) }}" type="image/png">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php $siteFont = setting('site.font', 'system'); @endphp
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600{{ $siteFont !== 'system' ? '&family=' . urlencode($siteFont) . ':wght@300;400;500;600;700;800' : '' }}&display=swap" rel="stylesheet">
    @if($siteFont !== 'system')
    <style>.theme-default { --font-frontend: '{{ $siteFont }}', ui-sans-serif, system-ui, sans-serif; }</style>
    @endif
    @viteAssets(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}">
    <style>body.theme-default {
        background: #0B0B0F;
        --brand-color: {{ setting('site.primary_color') }};
        --brand-color-soft: color-mix(in srgb, {{ setting('site.primary_color') }} 70%, white);
        --brand-color-deep: color-mix(in srgb, {{ setting('site.primary_color') }} 80%, black);
        --shadow-glow-val: 0 0 60px -16px color-mix(in srgb, {{ setting('site.primary_color') }} 25%, transparent);
        --shadow-glow: 0 0 60px -16px color-mix(in srgb, {{ setting('site.primary_color') }} 25%, transparent);
        --color-cream-50:  color-mix(in srgb, {{ setting('site.primary_color') }} 3%, #FBFAF6);
        --color-cream-100: color-mix(in srgb, {{ setting('site.primary_color') }} 5%, #F5F2EA);
        --color-cream-200: color-mix(in srgb, {{ setting('site.primary_color') }} 7%, #EDE8DC);
        --color-cream-300: color-mix(in srgb, {{ setting('site.primary_color') }} 9%, #D9D2BF);
    }</style>
    <x-integrations.ga4 />
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased">

    <x-preloader />


    <x-flash />

    <div class="min-h-screen flex overflow-x-hidden" data-user-shell>


        <aside class="hidden md:flex md:flex-col w-64 shrink-0 border-r border-white/8 bg-ink-900 fixed inset-y-0 left-0 z-20" id="user-sidebar">
            @include('theme::partials.user-sidebar-compact')
        </aside>

        <div class="hidden md:block w-64 shrink-0"></div>


        <div class="md:hidden fixed inset-0 bg-black/60 z-40 hidden" id="user-sidebar-overlay"></div>
        <aside class="md:hidden fixed inset-y-0 left-0 w-72 z-50 border-r border-white/8 bg-ink-900 -translate-x-full transition-transform flex flex-col" id="user-sidebar-mobile">
            @include('theme::partials.user-sidebar-compact')
        </aside>


        <div class="flex-1 min-w-0 flex flex-col overflow-x-hidden">


            <header class="sticky top-0 z-30 bg-ink-900/80 backdrop-blur border-b border-white/8">
                <div class="relative flex items-center gap-3 px-6 h-[64px]">

                    <button type="button" id="user-sidebar-toggle"
                            class="md:hidden w-9 h-9 grid place-items-center rounded-lg border border-white/10 text-white/70">
                        <x-heroicon-o-bars-3 class="h-5 w-5" />
                    </button>


                    <div class="hidden md:flex items-center gap-2 text-[13px] text-white/55 shrink-0">
                        <x-heroicon-o-home class="h-4 w-4" />
                        <span class="text-white/55">{{ setting('site.name', config('app.name')) }}</span>
                        @hasSection('breadcrumb')
                            <x-heroicon-o-chevron-right class="h-3.5 w-3.5 text-white/30" />
                            <span class="text-white capitalize">@yield('breadcrumb')</span>
                        @endif
                    </div>

                    <div class="flex-1"></div>


                    <div class="w-full max-w-xs md:max-w-sm hidden sm:block shrink-0">
                        <div class="flex items-center gap-2 rounded-full bg-white/4 border border-white/10 px-3 py-2">
                            <x-heroicon-o-magnifying-glass class="h-4 w-4 text-white/40 shrink-0" />
                            <span class="flex-1 text-[13px] text-white/35 select-none">@lang('Search…')</span>
                            <kbd class="hidden md:inline text-[10px] font-mono text-white/35 border border-white/10 rounded px-1.5 py-0.5">⌘K</kbd>
                        </div>
                    </div>

                    <div class="flex-1"></div>


                    @auth('user')
                    @php
                        $unreadCount = 0;
                        try { $unreadCount = auth('user')->user()->notifications()->whereNull('read_at')->count(); } catch(\Throwable) {}
                    @endphp
                    <div class="flex items-center gap-2 shrink-0">

                        <a href="{{ route('user.contracts.create') }}" class="hidden sm:flex btn-primary rounded-full px-3 py-2 text-sm items-center gap-1.5">
                            <x-heroicon-o-sparkles class="h-4 w-4" />
                            @lang('New contract')
                        </a>


                        <div class="relative" data-notification-dropdown>
                            <button type="button"
                                    class="w-9 h-9 grid place-items-center rounded-full bg-white/4 border border-white/10 text-white/70 relative hover:bg-white/8 transition"
                                    data-notification-trigger
                                    aria-label="{{ __('Notifications') }}">
                                <x-heroicon-o-bell class="h-5 w-5" />
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 grid place-items-center text-[9px] font-bold rounded-full bg-primary text-ink-900">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>


                            <div class="notification-panel hidden absolute right-0 top-full mt-2 w-96 max-h-[500px] overflow-y-auto rounded-2xl glass border border-white/10 shadow-lg z-50">
                                @php
                                    try {
                                        $notifications = auth('user')->user()->notifications()->latest()->limit(10)->get();
                                    } catch(\Throwable) {
                                        $notifications = collect();
                                    }
                                @endphp

                                @if($notifications->isEmpty())
                                    <div class="p-6 text-center">
                                        <x-heroicon-o-bell-slash class="h-8 w-8 text-white/30 mx-auto mb-2" />
                                        <p class="text-[13px] text-white/50">@lang('No notifications')</p>
                                    </div>
                                @else
                                    <div class="divide-y divide-white/8">
                                        @foreach($notifications as $notification)
                                        <a href="{{ $notification->url ?? '#' }}"
                                           class="block px-4 py-3 hover:bg-white/4 transition {{ is_null($notification->read_at) ? 'bg-white/5' : '' }}">
                                            <div class="flex items-start gap-3">
                                                <div class="mt-0.5 flex-shrink-0">
                                                    <x-heroicon-o-information-circle class="h-4 w-4 {{ is_null($notification->read_at) ? 'text-primary' : 'text-white/30' }}" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[13px] font-medium text-white">{{ $notification->title }}</p>
                                                    <p class="text-[12px] text-white/55 mt-0.5 line-clamp-2">{{ $notification->body }}</p>
                                                    <p class="text-[11px] text-white/30 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                    <div class="border-t border-white/8 p-3">
                                        <a href="{{ route('user.notifications.index') }}" class="block text-center text-[12px] text-primary hover:text-primary-soft transition">
                                            @lang('View all notifications')
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="relative" data-ud-dropdown>
                            <button type="button"
                                    class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 bg-white/4 border border-white/10 hover:bg-white/8 transition"
                                    data-ud-dropdown-trigger aria-haspopup="true" aria-expanded="false">
                                @if(auth('user')->user()->avatar_path)
                                    <img src="{{ upload_url(auth('user')->user()->avatar_path) }}"
                                         alt="{{ auth('user')->user()->name }}"
                                         class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-primary-deep grid place-items-center font-bold text-[11px] text-ink-900">
                                        {{ mb_strtoupper(mb_substr(auth('user')->user()->name ?? auth('user')->user()->email, 0, 1)) }}
                                    </span>
                                @endif
                                <span class="text-[12px] hidden md:inline text-white">{{ auth('user')->user()->name ?? auth('user')->user()->email }}</span>
                                <x-heroicon-o-chevron-down class="h-3.5 w-3.5 text-white/60" />
                            </button>


                            <div class="ud-user-dropdown hidden" data-ud-dropdown-panel>
                                <div class="ud-user-dropdown-header">
                                    <div class="text-[13px] font-medium text-white truncate">{{ auth('user')->user()->name ?? __('Member') }}</div>
                                    <div class="text-[11px] text-white/40 truncate mt-0.5">{{ auth('user')->user()->email }}</div>
                                </div>
                                <div class="ud-user-dropdown-body">
                                    <a href="{{ route('user.profile') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-user class="h-4 w-4" />@lang('Profile')
                                    </a>
                                    <a href="{{ route('user.2fa.index') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-shield-check class="h-4 w-4" />@lang('Security')
                                    </a>
                                    <a href="{{ route('user.notifications.index') }}" class="ud-user-dropdown-item">
                                        <x-heroicon-o-bell class="h-4 w-4" />@lang('Notifications')
                                        @if($unreadCount > 0)
                                        <span class="ml-auto pill pill-sign">{{ $unreadCount }}</span>
                                        @endif
                                    </a>
                                </div>
                                <div class="ud-user-dropdown-footer">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="ud-user-dropdown-item text-red-400 hover:text-red-300 w-full">
                                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />@lang('Sign out')
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>


            <main class="flex-1 overflow-y-auto">
                <div class="px-6 py-7 w-full">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>


    <x-confirm-modal />

    <script src="{{ asset('assets/static/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/static/global/js/phone-input.js') }}"></script>
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

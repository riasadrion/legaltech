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
        <aside class="hidden md:flex md:flex-col w-64 shrink-0 border-r border-white/8 bg-ink-900" id="user-sidebar">
            @include('theme::partials.user-sidebar-compact')
        </aside>

        {{-- Mobile sidebar overlay --}}
        <div class="md:hidden fixed inset-0 bg-black/60 z-40 hidden" id="user-sidebar-overlay"></div>
        <aside class="md:hidden fixed inset-y-0 left-0 w-72 z-50 border-r border-white/8 bg-ink-900 -translate-x-full transition-transform" id="user-sidebar-mobile">
            @include('theme::partials.user-sidebar-compact')
        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0 flex flex-col overflow-x-hidden">

            {{-- topbar --}}
            <div class="sticky top-0 z-30 border-b border-white/8 bg-ink-900/70 backdrop-blur">
                <div class="relative flex items-center gap-3 px-6 py-3.5">
                    {{-- mobile burger --}}
                    <button type="button" class="md:hidden text-white/70 hover:text-white" id="user-sidebar-toggle" aria-label="Toggle menu">
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </button>

                    {{-- breadcrumb / page title --}}
                    <div class="hidden md:flex items-center gap-2 text-[13px] text-white/55 shrink-0">
                        <a href="{{ route('user.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
                        @hasSection('breadcrumb')
                            <x-heroicon-o-chevron-right class="h-3.5 w-3.5 text-white/30" />
                            <span class="text-white/80">@yield('breadcrumb')</span>
                        @endif
                    </div>

                    <div class="flex-1"></div>

                    {{-- right cluster --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @auth('user')
                        <a href="{{ route('user.notifications.index') }}"
                           class="relative w-9 h-9 grid place-items-center rounded-full text-white/70 hover:bg-white/5 hover:text-white"
                           aria-label="{{ __('Notifications') }}">
                            <x-heroicon-o-bell class="h-5 w-5" />
                        </a>

                        <a href="{{ route('user.profile') }}" class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full hover:bg-white/5">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-lime text-ink-900 text-xs font-semibold">
                                {{ mb_strtoupper(mb_substr(auth('user')->user()->name ?? auth('user')->user()->email, 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline text-sm text-white/80">{{ auth('user')->user()->name ?? auth('user')->user()->email }}</span>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>

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

    @stack('scripts')
    <x-integrations.tawk />

    <script>
    $(function () {
        'use strict';
        $(document).on('click', '#user-sidebar-toggle', function () {
            $('#user-sidebar-mobile').toggleClass('-translate-x-full');
            $('#user-sidebar-overlay').toggleClass('hidden');
        });
        $(document).on('click', '#user-sidebar-overlay', function () {
            $('#user-sidebar-mobile').addClass('-translate-x-full');
            $('#user-sidebar-overlay').addClass('hidden');
        });
    });
    </script>
</body>
</html>

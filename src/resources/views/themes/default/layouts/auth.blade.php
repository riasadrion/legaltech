<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ upload_url($favicon) }}" type="image/png">
    @endif

    {{-- external-allowed: google-fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    {{-- external-allowed: google-fonts --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- external-allowed: google-fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>body.theme-default {
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

    @viteAssets(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}">
    <x-integrations.ga4 />
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased">

    <x-flash />

    <div class="min-h-screen flex">


        <aside class="hidden lg:flex relative w-1/2 bg-cream-100 overflow-hidden border-r border-ink-900/8">

            <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
            <div class="absolute inset-0 pointer-events-none auth-glow-top"></div>
            <div class="absolute inset-0 pointer-events-none auth-glow-bottom"></div>

            <div class="relative w-full p-12 flex flex-col">

                @php
                    $authSiteName = setting('site.name', config('app.name'));
                    $authLogo     = setting('site.logo');
                    $authDarkLogo = setting('site.dark_logo');
                @endphp
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if($authDarkLogo || $authLogo)
                        <img src="{{ upload_url($authDarkLogo ?: $authLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo h-9">
                    @else
                        <span class="grid place-items-center w-9 h-9 rounded-full bg-primary text-white">
                            <x-heroicon-s-pencil class="h-5 w-5" />
                        </span>
                        <span class="font-display text-2xl tracking-tight text-ink-900">{{ $authSiteName }}</span>
                    @endif
                </a>


                @hasSection('aside')
                    <div class="flex-1 mt-12 flex flex-col justify-center">
                        @yield('aside')
                    </div>
                @else
                    <div class="flex-1 mt-12 flex flex-col justify-center max-w-md">
                        <p class="font-display text-4xl text-ink-900 leading-tight">
                            {{ setting('site.tagline', __('Welcome back. Pick up where you left off.')) }}
                        </p>
                        <p class="text-ink-500 mt-4 text-sm">@lang('Secure, fast, and built for modern teams.')</p>
                    </div>
                @endif


                <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400">
                    @lang('Secured by') <span class="text-primary">●</span> {{ now()->format('Y') }} · {{ setting('site.name', config('app.name')) }}
                </div>
            </div>
        </aside>


        <div class="flex-1 flex flex-col bg-cream-50">

            <div class="flex items-center justify-between px-6 py-5 lg:px-10">
                <a href="{{ route('home') }}" class="flex items-center gap-2 lg:hidden">
                    @if($authLogo || $authDarkLogo)
                        @if($authLogo)
                            <img src="{{ upload_url($authLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-light h-8">
                        @endif
                        @if($authDarkLogo)
                            <img src="{{ upload_url($authDarkLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
                        @elseif($authLogo)
                            <img src="{{ upload_url($authLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
                        @endif
                    @else
                        <span class="grid place-items-center w-8 h-8 rounded-full bg-primary text-ink-900">
                            <x-heroicon-s-pencil class="h-4 w-4" />
                        </span>
                        <span class="font-display text-xl text-ink-900">{{ $authSiteName }}</span>
                    @endif
                </a>
                <a href="{{ route('home') }}" class="text-sm text-ink-500 hover:text-ink-900 inline-flex items-center gap-1.5 ml-auto">
                    <x-heroicon-o-arrow-left class="h-4 w-4" />
                    @lang('Back to home')
                </a>
            </div>

            <div class="flex-1 flex items-center justify-center px-6 lg:px-12 pb-12">
                <div class="w-full max-w-md">
                    @if(isset($heading) || View::hasSection('heading'))
                        <h1 class="font-display text-4xl text-ink-900 tracking-tight">{{ $heading ?? '' }}@yield('heading')</h1>
                        @if(isset($subheading) || View::hasSection('subheading'))
                            <p class="text-ink-500 mt-2 text-sm">{{ $subheading ?? '' }}@yield('subheading')</p>
                        @endif
                    @endif

                    <div class="mt-8">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-confirm-modal />

    <script src="{{ asset('assets/static/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/static/global/js/phone-input.js') }}"></script>
    <script>
    $(function () {
        'use strict';
        $(document).on('click', '[data-pwd-toggle]', function () {
            var $wrap  = $(this).closest('[data-pwd-wrap]');
            var $input = $wrap.find('input');
            var show   = $input.attr('type') === 'password';
            $input.attr('type', show ? 'text' : 'password');
            $wrap.find('[data-eye]').toggleClass('hidden', show);
            $wrap.find('[data-eye-off]').toggleClass('hidden', !show);
        });
    });
    </script>
    @stack('scripts')
    <x-integrations.tawk />
</body>
</html>

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
    <style>body.theme-default { --brand-color: {{ setting('site.primary_color', '#D4FF3A') }}; }</style>

    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
    @php $themeCssPath = base_path('../assets/static/css/themes/default.css'); @endphp
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}?v={{ is_file($themeCssPath) ? filemtime($themeCssPath) : 1 }}">
    <x-integrations.ga4 />
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased">

    <x-flash />

    <div class="min-h-screen flex">


        <aside class="hidden lg:flex relative w-1/2 bg-ink-950 overflow-hidden">

            <div class="absolute inset-0 bg-grid radial-fade pointer-events-none"></div>
            <div class="absolute inset-0 pointer-events-none auth-glow-top"></div>
            <div class="absolute inset-0 pointer-events-none auth-glow-bottom"></div>

            <div class="relative w-full p-12 flex flex-col">

                @php
                    $authSiteName = setting('site.name', config('app.name'));
                    $authLogo     = setting('site.logo');
                    $authDarkLogo = setting('site.dark_logo');
                @endphp
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if($authLogo || $authDarkLogo)
                        @if($authLogo)
                            <img src="{{ upload_url($authLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-light h-9">
                        @endif
                        @if($authDarkLogo)
                            <img src="{{ upload_url($authDarkLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-9">
                        @elseif($authLogo)
                            <img src="{{ upload_url($authLogo) }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-9">
                        @endif
                    @else
                        <span class="grid place-items-center w-9 h-9 rounded-full bg-lime text-ink-900">
                            <x-heroicon-s-pencil class="h-5 w-5" />
                        </span>
                        <span class="font-display text-2xl tracking-tight text-white">{{ $authSiteName }}</span>
                    @endif
                </a>


                @hasSection('aside')
                    <div class="flex-1 mt-12 flex flex-col justify-center">
                        @yield('aside')
                    </div>
                @else
                    <div class="flex-1 mt-12 flex flex-col justify-center max-w-md">
                        <p class="font-display text-4xl text-white leading-tight">
                            {{ setting('site.tagline', __('Welcome back. Pick up where you left off.')) }}
                        </p>
                        <p class="text-white/60 mt-4 text-sm">{{ __('Secure, fast, and built for modern teams.') }}</p>
                    </div>
                @endif


                <div class="text-[11px] font-mono uppercase tracking-widest text-white/35">
                    {{ __('Secured by') }} <span class="text-lime">●</span> {{ now()->format('Y') }} · {{ setting('site.name', config('app.name')) }}
                </div>
            </div>
        </aside>


        <div class="flex-1 flex flex-col">

            <div class="flex items-center justify-between px-6 py-5 lg:px-10">
                <a href="{{ route('home') }}" class="flex items-center gap-2 lg:hidden">
                    @if($authLogo || $authDarkLogo)
                        @if($authLogo)
                            <img src="{{ $authLogo }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-light h-8">
                        @endif
                        @if($authDarkLogo)
                            <img src="{{ $authDarkLogo }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
                        @elseif($authLogo)
                            <img src="{{ $authLogo }}" alt="{{ $authSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
                        @endif
                    @else
                        <span class="grid place-items-center w-8 h-8 rounded-full bg-lime text-ink-900">
                            <x-heroicon-s-pencil class="h-4 w-4" />
                        </span>
                        <span class="font-display text-xl text-white">{{ $authSiteName }}</span>
                    @endif
                </a>
                <a href="{{ route('home') }}" class="text-sm text-white/60 hover:text-white inline-flex items-center gap-1.5 ml-auto">
                    <x-heroicon-o-arrow-left class="h-4 w-4" />
                    {{ __('Back to home') }}
                </a>
            </div>

            <div class="flex-1 flex items-center justify-center px-6 lg:px-12 pb-12">
                <div class="w-full max-w-md">
                    @if(isset($heading) || View::hasSection('heading'))
                        <h1 class="font-display text-4xl text-white tracking-tight">{{ $heading ?? '' }}@yield('heading')</h1>
                        @if(isset($subheading) || View::hasSection('subheading'))
                            <p class="text-white/55 mt-2 text-sm">{{ $subheading ?? '' }}@yield('subheading')</p>
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

    <script src="{{ asset('assets/static/js/phone-input.js') }}"></script>
    @stack('scripts')
    <x-integrations.tawk />
</body>
</html>

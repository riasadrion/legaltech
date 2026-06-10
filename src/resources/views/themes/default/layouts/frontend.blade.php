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
    @viteAssets(['resources/css/app.css', 'resources/js/app.js'])
    @if($siteFont !== 'system')
    <style>.theme-default { --font-frontend: '{{ $siteFont }}', ui-sans-serif, system-ui, sans-serif; }</style>
    @endif
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
    <x-integrations.ga4 />
    <link rel="stylesheet" href="{{ asset('assets/static/global/css/custom.css') }}">
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased selection:bg-primary selection:text-ink-900">

    <x-preloader />


    <x-flash />


    @hasSection('nav')
        @yield('nav')
    @else
        @include('theme::partials.nav')
    @endif


    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>


    @include('theme::partials.footer')


    <script src="{{ asset('assets/static/global/js/jquery.min.js') }}"></script>
    <x-confirm-modal />

    <x-integrations.tawk />
    @include('theme::partials.cookie-consent')
    @stack('scripts')
</body>
</html>

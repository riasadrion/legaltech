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
    {{-- external-allowed: google-fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
    @php $themeCssPath = base_path('../assets/static/css/themes/default.css'); @endphp
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}?v={{ is_file($themeCssPath) ? filemtime($themeCssPath) : 1 }}">
    <x-integrations.ga4 />
    @php $customCssPath = base_path('../assets/static/css/custom.css'); @endphp
    <link rel="stylesheet" href="{{ asset('assets/static/css/custom.css') }}?v={{ is_file($customCssPath) ? filemtime($customCssPath) : config('app.version', 1) }}">
    @stack('head')
</head>
<body class="theme-default min-h-screen antialiased selection:bg-lime selection:text-ink-900">

    {{-- Flash messages --}}
    <x-flash />

    {{-- Navigation --}}
    @hasSection('nav')
        @yield('nav')
    @else
        @include('theme::partials.nav')
    @endif

    {{-- Main content --}}
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('theme::partials.footer')

    {{-- Global Confirm Modal --}}
    <x-confirm-modal />

    @stack('scripts')
    <x-integrations.tawk />
    @include('theme::partials.cookie-consent')
</body>
</html>

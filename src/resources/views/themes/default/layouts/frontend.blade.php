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
    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
    <x-integrations.ga4 />
    <link rel="stylesheet" href="{{ asset('assets/static/css/custom.css') }}?v={{ filemtime(public_path('assets/static/css/custom.css')) }}">
    @stack('head')
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">

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

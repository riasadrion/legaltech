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
    @stack('head')
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">

    {{-- Flash messages --}}
    <x-flash />

    {{-- Navigation --}}
    @include('theme::partials.nav')

    {{-- Main container with sidebar --}}
    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-6">
            {{-- Sidebar --}}
            <aside class="hidden sm:flex sm:w-64 flex-col flex-shrink-0">
                @include('theme::partials.user-sidebar-compact')
            </aside>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </main>

    {{-- Footer --}}
    @include('theme::partials.footer')

    {{-- Global Confirm Modal --}}
    <x-confirm-modal />

    @stack('scripts')
    <x-integrations.tawk />
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ $favicon }}" type="image/png">
    @endif
    <script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
    <x-integrations.ga4 />
    @stack('head')
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">

    <x-flash />

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block">
                <span class="text-2xl font-bold text-brand-600">{{ config('app.name') }}</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            @if(isset($heading) || View::hasSection('heading'))
            <h1 class="text-xl font-semibold text-gray-900 mb-6">{{ $heading ?? '' }}@yield('heading')</h1>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    {{-- Global Confirm Modal --}}
    <x-confirm-modal />

    @stack('scripts')
    <x-integrations.tawk />
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? '') ? $title . ' — ' : '' }}{{ setting('site.name', config('app.name')) }} Admin</title>
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ upload_url($favicon) }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    @viteAssets(['resources/css/app.css', 'resources/js/admin.js'])
    <script src="{{ asset('assets/static/global/js/jquery.min.js') }}"></script>
    <script>
    window._adminReady = window._adminReady || [];
    window.onAdminReady = function (fn) { window._adminReady.push(fn); };
    </script>
    <script src="{{ asset('assets/static/admin/admin.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('assets/static/admin/css/admin.css') }}">
    @stack('head')
</head>
<body class="antialiased text-gray-900">


<div id="page-loader">
    <div id="loader-bar"></div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-shell">


    @include("admin.partials.sidebar")


    <div class="admin-main" id="adminMainContent">


        @include("admin.partials.topbar")


        <x-admin::flash />


        <main class="admin-content">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>


<x-admin::confirm-modal :admin="true" />

<script>
jQuery(function ($) {
    'use strict';
    $(document).on('click', '#sidebar-toggle-mobile', function () {
        $('#adminSidebar').toggleClass('open');
        $('#sidebarOverlay').toggleClass('show');
    });
});
</script>

@stack('scripts')
</body>
</html>
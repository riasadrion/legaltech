<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ setting('site.name', config('app.name')) }}</title>
    @if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ upload_url($favicon) }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @viteAssets(['resources/css/app.css'])
    @stack('head')
</head>
<body class="admin-auth">

<div class="al-left">
    <div class="al-left-noise"></div>

    @php
        $siteName = setting('site.name', config('app.name'));
        $brandLogo = setting('site.logo') ?: setting('site.dark_logo');
    @endphp
    <a href="{{ route('home') }}" class="al-brand">
        @if($brandLogo)
            <img src="{{ upload_url($brandLogo) }}" alt="{{ $siteName }}" class="al-brand-logo">
        @else
            <div class="al-brand-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 2L3 7v8l8 5 8-5V7l-8-5z" stroke="white" stroke-width="1.8" stroke-linejoin="round" fill="rgba(255,255,255,0.15)"/>
                    <path d="M11 2v13M3 7l8 4 8-4" stroke="white" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="al-brand-name">{{ $siteName }}</span>
        @endif
    </a>

    <div class="al-illo">
        <img src="{{ asset('assets/static/admin/images/admin-login-illustration.svg') }}" alt="Dashboard preview">
    </div>

    <div class="al-tagline">
        <h2>Your command centre<br>for everything.</h2>
        <p>Manage users, payments, subscriptions and content from one powerful admin panel.</p>
    </div>
</div>

<div class="al-right">

    @if($brandLogo)
    <a href="{{ route('home') }}" class="al-mobile-brand">
        <img src="{{ upload_url(setting('site.dark_logo') ?: setting('site.logo')) }}" alt="{{ setting('site.name', config('app.name')) }}" class="al-mobile-brand-logo">
    </a>
    @endif

    <div class="al-form-wrap">

        <div class="al-form-head">
            <h1>@yield('heading')</h1>
            <p>@yield('subheading')</p>
        </div>

        @if($errors->any())
        <div class="al-error">{{ $errors->first() }}</div>
        @endif

        @if(session('status'))
        <div class="al-success">{{ session('status') }}</div>
        @endif

        @yield('content')

        <div class="al-footer">&copy; {{ date('Y') }} {{ setting('site.name', config('app.name')) }}. All rights reserved.</div>
    </div>
</div>

@stack('scripts')

</body>
</html>

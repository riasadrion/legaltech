<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @viteAssets(['resources/css/app.css'])
    <style>:root {
        --brand-color: {{ setting('site.primary_color') }};
        --color-cream-50: color-mix(in srgb, {{ setting('site.primary_color') }} 3%, #FBFAF6);
    }</style>
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}">
</head>
<body class="theme-default-error">

    <div class="err-bg"></div>
    <div class="err-glow"></div>

    <div class="err-wrap">

        <div class="err-code" data-code="{{ $code }}">{{ $code }}</div>

        <div class="err-card">

            <div class="err-icon-wrap">
                @if($code == 404)
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v3m0 3h.01"/>
                    </svg>
                @elseif($code == 403)
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                @elseif($code == 419)
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                @elseif($code == 503)
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                @endif
            </div>

            <h1 class="err-title">{{ $title }}</h1>
            <p class="err-message">{{ $message }}</p>

            @if($code !== 503)
            <div class="err-actions">
                <a href="{{ url('/') }}" class="err-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Go home
                </a>
                <button type="button" id="err-go-back" class="err-btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Go back
                </button>
            </div>
            @else
            <p class="err-maintenance">We'll be back shortly</p>
            @endif

        </div>

        <p class="err-brand">{{ config('app.name') }}</p>

    </div>

    <script>
    $(function () {
    'use strict';
        $('#err-go-back').on('click', function () { history.back(); });
    });
    </script>

</body>
</html>

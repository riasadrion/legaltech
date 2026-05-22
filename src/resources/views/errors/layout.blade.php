<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css']); ?>
    <style>
        body { background: #08090C; }
        .err-code {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(140px, 22vw, 280px);
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(212,255,58,0.25);
            letter-spacing: -0.04em;
            user-select: none;
            position: relative;
        }
        .err-code::after {
            content: attr(data-code);
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(212,255,58,0.12) 0%, rgba(124,58,237,0.08) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .err-bg {
            position: fixed;
            inset: 0;
            background-image: url('/assets/static/img/error-bg.svg');
            background-size: cover;
            background-position: center;
            pointer-events: none;
        }
        .glass-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
        }
        .btn-lime-err {
            display: inline-flex; align-items: center; gap: 8px;
            background: #D4FF3A; color: #08090C;
            font-weight: 700; font-size: 14px;
            padding: 12px 28px; border-radius: 999px;
            text-decoration: none; transition: opacity 0.15s;
        }
        .btn-lime-err:hover { opacity: 0.88; }
        .btn-ghost-err {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.5);
            font-weight: 600; font-size: 14px;
            padding: 12px 28px; border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.1);
            text-decoration: none; transition: all 0.15s; cursor: pointer;
        }
        .btn-ghost-err:hover { background: rgba(255,255,255,0.09); color: rgba(255,255,255,0.8); }
    </style>
</head>
<body style="min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:24px; position:relative; overflow:hidden;">

    <div class="err-bg"></div>

    <div style="position:relative; z-index:10; text-align:center; max-width:560px; width:100%;">

        {{-- Big error code --}}
        <div class="err-code" data-code="{{ $code }}">{{ $code }}</div>

        {{-- Glass card --}}
        <div class="glass-card" style="border-radius:24px; padding:40px 48px; margin-top:-20px; position:relative;">

            {{-- Icon badge --}}
            <div style="margin-bottom:20px;">
                <span style="display:inline-flex; align-items:center; justify-content:center;
                             width:52px; height:52px; border-radius:14px;
                             background:rgba(212,255,58,0.08); border:1px solid rgba(212,255,58,0.2);">
                    @if($code == 404)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4FF3A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v3m0 3h.01"/>
                        </svg>
                    @elseif($code == 403)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4FF3A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    @elseif($code == 419)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4FF3A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    @elseif($code == 503)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4FF3A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    @else
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4FF3A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    @endif
                </span>
            </div>

            <h1 style="font-family:'Instrument Serif',serif; font-size:2rem; color:#fff; margin:0 0 10px; font-weight:400;">
                {{ $title }}
            </h1>
            <p style="color:rgba(255,255,255,0.4); font-size:15px; line-height:1.7; margin:0 0 32px;">
                {{ $message }}
            </p>

            @if($code !== 503)
            <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center;">
                <a href="{{ url('/') }}" class="btn-lime-err">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Go home
                </a>
                <button onclick="history.back()" class="btn-ghost-err">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Go back
                </button>
            </div>
            @else
            <p style="color:rgba(212,255,58,0.6); font-size:13px; font-weight:600; letter-spacing:0.05em; text-transform:uppercase;">
                We'll be back shortly
            </p>
            @endif

        </div>

        {{-- Brand --}}
        <p style="margin-top:32px; color:rgba(255,255,255,0.12); font-size:12px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">
            {{ config('app.name') }}
        </p>

    </div>

</body>
</html>

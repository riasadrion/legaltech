<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css']); ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'Outfit', sans-serif; background: #f5f5f9; min-height: 100vh; display: flex; }

        .al-left {
            flex: 0 0 52%;
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 45%, #7c3aed 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        .al-left-noise {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .al-brand { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
        .al-brand-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
        }
        .al-brand-name { font-size: 18px; font-weight: 700; color: white; letter-spacing: -0.3px; }

        .al-illo { position: relative; z-index: 1; flex: 1; display: flex; align-items: center; justify-content: center; }
        .al-illo img, .al-illo svg { width: 100%; max-width: 460px; }

        .al-tagline { position: relative; z-index: 1; }
        .al-tagline h2 { font-size: 26px; font-weight: 800; color: white; line-height: 1.25; margin: 0 0 10px; letter-spacing: -0.5px; }
        .al-tagline p { font-size: 14px; color: rgba(255,255,255,0.65); margin: 0; line-height: 1.6; max-width: 340px; }

        .al-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #ffffff;
        }

        .al-form-wrap { width: 100%; max-width: 380px; }

        .al-form-head { margin-bottom: 32px; }
        .al-form-head h1 { font-size: 24px; font-weight: 800; color: #0f0f1a; letter-spacing: -0.5px; margin: 0 0 6px; }
        .al-form-head p { font-size: 14px; color: #6b7280; margin: 0; }

        .al-field { margin-bottom: 18px; }
        .al-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .al-input {
            width: 100%; padding: 11px 14px; font-size: 14px; font-family: inherit;
            border: 1.5px solid #e5e7eb; border-radius: 10px; background: #f9fafb;
            color: #111827; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .al-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); background: #fff; }
        .al-input::placeholder { color: #9ca3af; }

        .al-input-wrap { position: relative; }
        .al-eye {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #9ca3af; padding: 2px;
            display: flex; align-items: center;
        }
        .al-eye:hover { color: #6366f1; }

        .al-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .al-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #374151; cursor: pointer; }
        .al-check input { width: 15px; height: 15px; accent-color: #6366f1; cursor: pointer; }
        .al-forgot { font-size: 13px; color: #6366f1; font-weight: 500; text-decoration: none; }
        .al-forgot:hover { color: #4f46e5; text-decoration: underline; }

        .al-btn {
            width: 100%; padding: 13px; font-size: 15px; font-weight: 700; font-family: inherit;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: white; border: none; border-radius: 12px; cursor: pointer;
            box-shadow: 0 4px 14px rgba(99,102,241,0.4);
            transition: opacity 0.15s, transform 0.1s;
            letter-spacing: -0.1px;
        }
        .al-btn:hover { opacity: 0.92; transform: translateY(-1px); }
        .al-btn:active { transform: translateY(0); }

        .al-error {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px;
        }

        .al-footer { margin-top: 32px; font-size: 12px; color: #9ca3af; text-align: center; }

        .al-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0; }
        .al-divider span { font-size: 12px; color: #d1d5db; white-space: nowrap; }
        .al-divider::before, .al-divider::after { content: ''; flex: 1; height: 1px; background: #f3f4f6; }

        .al-secure { display: flex; align-items: center; justify-content: center; gap-6px; gap: 6px; }
        .al-input-pwd { padding-right: 42px; }
        .al-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:10px 14px; border-radius:10px; font-size:13px; margin-bottom:18px; }
        .al-secure-dot { width: 6px; height: 6px; border-radius: 50%; background: #34d399; }
        .al-secure span { font-size: 12px; color: #9ca3af; }

        @media (max-width: 900px) {
            .al-left { display: none; }
            .al-right { background: #f5f5f9; }
            .al-form-wrap { background: white; padding: 32px; border-radius: 20px; box-shadow: 0 4px 32px rgba(0,0,0,0.08); }
        }
    </style>
</head>
<body>

{{-- Left panel --}}
<div class="al-left">
    <div class="al-left-noise"></div>

    <div class="al-brand">
        <div class="al-brand-icon">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 2L3 7v8l8 5 8-5V7l-8-5z" stroke="white" stroke-width="1.8" stroke-linejoin="round" fill="rgba(255,255,255,0.15)"/>
                <path d="M11 2v13M3 7l8 4 8-4" stroke="white" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
        </div>
        <span class="al-brand-name">{{ config('app.name') }}</span>
    </div>

    <div class="al-illo">
        <img src="{{ asset('assets/static/images/admin-login-illustration.svg') }}" alt="Dashboard preview">
    </div>

    <div class="al-tagline">
        <h2>Your command centre<br>for everything.</h2>
        <p>Manage users, payments, subscriptions and content from one powerful admin panel.</p>
    </div>
</div>

{{-- Right panel --}}
<div class="al-right">
    <div class="al-form-wrap">

        <div class="al-form-head">
            <h1>Welcome back 👋</h1>
            <p>Sign in to your admin account to continue.</p>
        </div>

        @if($errors->any())
        <div class="al-error">{{ $errors->first() }}</div>
        @endif

        @if(session('status'))
        <div class="al-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="al-field">
                <label class="al-label" for="email">Email address</label>
                <input id="email" name="email" type="email" class="al-input" value="{{ old('email') }}"
                       placeholder="admin@example.com" autocomplete="email" required>
            </div>

            <div class="al-field">
                <label class="al-label" for="password">Password</label>
                <div class="al-input-wrap">
                    <input id="password" name="password" type="password" class="al-input al-input-pwd"
                           placeholder="Your password" autocomplete="current-password" required>
                    <button type="button" class="al-eye" id="togglePwd" aria-label="Toggle password">
                        <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="al-row">
                <label class="al-check">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('admin.password.request') }}" class="al-forgot">Forgot password?</a>
            </div>

            <button type="submit" class="al-btn">Sign in to Dashboard</button>
        </form>

        <div class="al-divider"><span>secured</span></div>

        <div class="al-secure">
            <div class="al-secure-dot"></div>
            <span>256-bit SSL encrypted · Admin access only</span>
        </div>

        <div class="al-footer">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </div>
</div>

<script>
(function () {
    'use strict';
    var btn = document.getElementById('togglePwd');
    var input = document.getElementById('password');
    var icon = document.getElementById('eyeIcon');
    var eyeOff = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    var eyeOn = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    btn.addEventListener('click', function () {
        var isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        icon.innerHTML = isText ? eyeOn : eyeOff;
    });
})();
</script>

</body>
</html>

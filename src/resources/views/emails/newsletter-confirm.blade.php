<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#374151;max-width:560px;margin:0 auto;padding:32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin-bottom:16px;">Confirm your subscription</h2>
    <p style="margin-bottom:24px;">Click the button below to confirm your subscription to {{ config('app.name') }}.</p>
    <a href="{{ route('newsletter.confirm', $subscriber->token) }}"
       style="display:inline-block;background:#4f46e5;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600;">
        Confirm subscription
    </a>
    <p style="margin-top:24px;font-size:12px;color:#9ca3af;">
        If you didn't subscribe, you can ignore this email.
    </p>
</body>
</html>

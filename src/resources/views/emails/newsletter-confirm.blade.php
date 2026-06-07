<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="{{ config('app.url') }}/assets/static/global/css/email.css">
</head>
<body class="newsletter-body">
    <h2 class="newsletter-heading">Confirm your subscription</h2>
    <p class="newsletter-lead">Click the button below to confirm your subscription to {{ $appName }}.</p>
    <a href="{{ $confirmUrl }}" class="newsletter-cta">
        Confirm subscription
    </a>
    <p class="newsletter-footnote">
        If you didn't subscribe, you can ignore this email.
    </p>
</body>
</html>

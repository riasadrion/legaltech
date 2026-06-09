<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="{{ config('app.url') }}/assets/static/global/css/email.css">
</head>
<body class="newsletter-body">
    <?php echo $body; ?>
    <hr class="newsletter-divider">
    <p class="newsletter-meta">
        You're receiving this because you subscribed to {{ $appName }}.<br>
        <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
    </p>
</body>
</html>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#374151;max-width:560px;margin:0 auto;padding:32px 16px;">
    <?php echo $body; ?>
    <hr style="margin:32px 0;border:none;border-top:1px solid #e5e7eb;">
    <p style="font-size:12px;color:#9ca3af;">
        You're receiving this because you subscribed to {{ config('app.name') }}.<br>
        <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}" style="color:#9ca3af;">Unsubscribe</a>
    </p>
</body>
</html>

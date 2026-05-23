<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Already Signed') }}</title>
    <link rel="stylesheet" href="/assets/static/css/themes/default.css">
</head>
<body class="min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-md text-center space-y-4">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/5">
        <x-heroicon-o-check-circle class="h-8 w-8 text-white/40" />
    </div>
    <h1 class="font-display text-2xl text-white">{{ __('Already signed') }}</h1>
    <p class="text-white/50 text-sm">
        {{ __('You signed this contract on :date.', ['date' => $party->signed_at->format('d M Y, H:i')]) }}
    </p>
</div>

</body>
</html>

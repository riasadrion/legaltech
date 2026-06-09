<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('Already Signed')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    @viteAssets(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}">
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 theme-default">

<div class="mb-8 text-center">
    <a href="{{ url('/') }}" class="font-display text-2xl text-white tracking-tight">{{ config('app.name') }}</a>
</div>

<div class="w-full max-w-md text-center space-y-5">

    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white/5 border border-white/10">
        <x-heroicon-o-check-circle class="h-10 w-10 text-white/40" />
    </div>

    <div>
        <h1 class="font-display text-3xl text-white">@lang('Already signed')</h1>
        <p class="text-white/50 text-sm mt-2">
            {{ __('You signed this contract on :date.', ['date' => $party->signed_at->format('d M Y, H:i')]) }} UTC
        </p>
    </div>

    <div class="rounded-2xl glass border border-white/10 p-5 text-left space-y-2 text-[12px]">
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('Contract')</span>
            <span class="text-white font-medium">{{ $party->contract->title ?? '—' }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('Signed by')</span>
            <span class="text-white">{{ $party->name }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('Date')</span>
            <span class="text-white">{{ $party->signed_at->format('d M Y, H:i') }} UTC</span>
        </div>
    </div>

    <p class="text-[11px] text-white/20">
        {{ config('app.name') }} &mdash; @lang('Secure electronic contract signing')
    </p>

</div>

</body>
</html>

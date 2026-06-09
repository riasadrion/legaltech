<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('Signed') — {{ $contract->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    @viteAssets(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/static/css/themes/default.css') }}">
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 theme-default">

<div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[400px] rounded-full bg-primary/6 blur-[120px]"></div>
</div>

<div class="mb-8 text-center">
    <a href="{{ url('/') }}" class="font-display text-2xl text-white tracking-tight">{{ config('app.name') }}</a>
</div>

<div class="w-full max-w-md text-center space-y-5">

    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-primary/10 border border-primary/20">
        <x-heroicon-o-check-badge class="h-10 w-10 text-primary" />
    </div>

    <div>
        <h1 class="font-display text-4xl text-white">@lang('Signed!')</h1>
        <p class="text-white/50 text-sm leading-relaxed mt-2">
            {{ __('You have signed ":title". Your signature has been recorded with a timestamp and IP address.', ['title' => $contract->title]) }}
        </p>
    </div>

    <div class="rounded-2xl glass border border-white/10 p-5 text-left space-y-3 text-[12px]">
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('Signed by')</span>
            <span class="text-white font-medium">{{ $party->name }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('Email')</span>
            <span class="text-white/70">{{ $party->email }}</span>
        </div>
        <div class="border-t border-white/8 pt-3 flex justify-between items-center">
            <span class="text-white/40">@lang('Date')</span>
            <span class="text-white">{{ $party->signed_at->format('d M Y, H:i') }} UTC</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-white/40">@lang('IP Address')</span>
            <span class="text-white font-mono text-[11px]">{{ $party->ip_address }}</span>
        </div>
    </div>

    @if($contract->isSigned())
    <div class="rounded-2xl glass border border-primary/20 bg-primary/5 px-5 py-3">
        <p class="text-[13px] text-primary font-medium">@lang('All parties have signed — the contract is now complete.')</p>
    </div>
    @else
    <p class="text-[12px] text-white/35">@lang('Awaiting signatures from other parties.')</p>
    @endif

    <p class="text-[11px] text-white/20 mt-4">
        {{ config('app.name') }} &mdash; @lang('Secure electronic contract signing')
    </p>

</div>

</body>
</html>

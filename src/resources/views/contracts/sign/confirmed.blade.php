<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Signed') }} — {{ $contract->title }}</title>
    <link rel="stylesheet" href="/assets/static/css/themes/default.css">
</head>
<body class="min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-md text-center space-y-5">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-lime/10">
        <x-heroicon-o-check-badge class="h-8 w-8 text-lime" />
    </div>
    <h1 class="font-display text-3xl text-white">{{ __('Signed successfully') }}</h1>
    <p class="text-white/50 text-sm leading-relaxed">
        {{ __('You have signed ":title". Your signature has been recorded along with the timestamp and your IP address.', ['title' => $contract->title]) }}
    </p>
    <div class="rounded-2xl glass p-5 text-left space-y-2 text-[12px]">
        <div class="flex justify-between">
            <span class="text-white/40">{{ __('Signed by') }}</span>
            <span class="text-white">{{ $party->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-white/40">{{ __('Date') }}</span>
            <span class="text-white">{{ $party->signed_at->format('d M Y, H:i') }} UTC</span>
        </div>
        <div class="flex justify-between">
            <span class="text-white/40">{{ __('IP Address') }}</span>
            <span class="text-white font-mono">{{ $party->ip_address }}</span>
        </div>
    </div>
    @if($contract->isSigned())
    <p class="text-[12px] text-lime/70">{{ __('All parties have signed. The contract is now complete.') }}</p>
    @else
    <p class="text-[12px] text-white/40">{{ __('Awaiting signatures from other parties.') }}</p>
    @endif
</div>

</body>
</html>

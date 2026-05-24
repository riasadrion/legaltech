<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Sign Contract') }} — {{ $contract->title }}</title>
    <link rel="stylesheet" href="/assets/static/css/themes/default.css">
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

{{-- Background decoration --}}
<div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-lime/5 blur-[120px]"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-lime/3 blur-[100px]"></div>
</div>

{{-- Brand --}}
<div class="mb-8 text-center">
    <a href="{{ url('/') }}" class="font-display text-2xl text-white tracking-tight">{{ config('app.name') }}</a>
</div>

<div class="w-full max-w-2xl space-y-5">

    <div class="text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-lime/10 border border-lime/20 mb-4">
            <x-heroicon-o-document-check class="h-7 w-7 text-lime" />
        </div>
        <h1 class="font-display text-3xl md:text-4xl text-white">{{ $contract->title }}</h1>
        <p class="text-white/50 mt-2 text-sm">
            {{ __('Hello :name, you have been invited to review and sign this contract.', ['name' => $party->name]) }}
        </p>
    </div>

    {{-- Contract preview --}}
    <div class="rounded-2xl glass border border-white/10 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3 border-b border-white/8 bg-white/3">
            <x-heroicon-o-document-text class="h-4 w-4 text-white/40 shrink-0" />
            <span class="text-[12px] text-white/40 font-medium uppercase tracking-widest">{{ __('Contract Document') }}</span>
        </div>
        <div class="px-6 py-6 max-h-[55vh] overflow-y-auto contract-editor-body text-white/80 text-[13px] leading-relaxed">
            {!! $contract->body_html !!}
        </div>
    </div>

    {{-- Agreement & sign --}}
    <div class="rounded-2xl glass border border-white/10 p-6 space-y-4">
        <p class="text-[13px] text-white/55 leading-relaxed">
            {{ __('By clicking "I Agree and Sign", you confirm that you have read the contract above and agree to be legally bound by its terms. Your IP address and timestamp will be recorded as proof of your signature.') }}
        </p>
        <form method="POST" action="{{ route('contracts.sign.confirm', $party->token) }}">
            @csrf
            <button type="submit"
                    class="btn-lime rounded-xl w-full py-3 text-[13px] font-semibold flex items-center justify-center gap-2">
                <x-heroicon-o-check-badge class="h-5 w-5" />
                {{ __('I Agree and Sign') }}
            </button>
        </form>
    </div>

    <p class="text-center text-[11px] text-white/25">
        {{ config('app.name') }} &mdash; {{ __('Secure electronic contract signing') }}
    </p>

</div>

</body>
</html>

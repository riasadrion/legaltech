<!DOCTYPE html>
<html lang="en" class="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Sign Contract') }} — {{ $contract->title }}</title>
    <link rel="stylesheet" href="/assets/static/css/themes/default.css">
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">

<div class="w-full max-w-2xl space-y-6">

    <div class="text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-lime/10 mb-4">
            <x-heroicon-o-document-check class="h-6 w-6 text-lime" />
        </div>
        <h1 class="font-display text-3xl text-white">{{ $contract->title }}</h1>
        <p class="text-white/50 mt-2 text-sm">{{ __('Hello :name, you have been invited to sign this contract.', ['name' => $party->name]) }}</p>
    </div>

    {{-- Contract preview --}}
    <div class="rounded-2xl glass overflow-hidden max-h-[60vh] overflow-y-auto">
        <div class="px-6 py-6 contract-editor-body text-white/80 text-[13px] leading-relaxed">
            {!! $contract->body_html !!}
        </div>
    </div>

    {{-- Sign button --}}
    <div class="rounded-2xl glass p-6 space-y-4">
        <p class="text-[13px] text-white/60 leading-relaxed">
            {{ __('By clicking "I Agree and Sign", you confirm that you have read the contract above and agree to be legally bound by its terms. Your IP address and timestamp will be recorded.') }}
        </p>
        <form method="POST" action="{{ route('contracts.sign.confirm', $party->token) }}">
            @csrf
            <button type="submit" class="btn-lime rounded-xl w-full py-3 text-[13px] font-semibold flex items-center justify-center gap-2">
                <x-heroicon-o-check-badge class="h-5 w-5" />
                {{ __('I Agree and Sign') }}
            </button>
        </form>
    </div>

</div>

</body>
</html>

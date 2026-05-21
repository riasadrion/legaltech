@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8 py-10">
    <x-page-header title="Save your recovery codes" />

    <div class="mt-8 rounded-xl border border-yellow-200 bg-yellow-50 p-6">
        <div class="flex gap-3 mb-4">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-600 shrink-0 mt-0.5" />
            <p class="text-sm text-yellow-800">
                Store these recovery codes in a safe place. Each code can only be used once.
                If you lose access to your authenticator app and have no recovery codes, you will be locked out.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 font-mono text-sm">
            @foreach ($codes as $code)
                <div class="rounded bg-white px-3 py-2 text-center text-gray-800 border border-yellow-200">{{ $code }}</div>
            @endforeach
        </div>

        <div class="mt-4 flex gap-3">
            <button type="button" data-copy-codes
                    class="flex items-center gap-2 rounded-lg border border-yellow-300 bg-white px-4 py-2 text-sm font-medium text-yellow-800 hover:bg-yellow-50">
                <x-heroicon-o-clipboard class="h-4 w-4" />
                Copy codes
            </button>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('user.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
            I've saved my codes — go to dashboard
        </a>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    'use strict';
    $('[data-copy-codes]').on('click', function () {
        var codes = [];
        $('.font-mono [class*="text-gray"]').each(function () {
            codes.push($(this).text().trim());
        });
        navigator.clipboard.writeText(codes.join('\n'));
        $(this).text('Copied!');
    });
});
</script>
@endpush
@endsection

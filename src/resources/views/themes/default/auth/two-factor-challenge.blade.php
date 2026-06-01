@extends('theme::layouts.auth')

@section('heading', 'Two-factor authentication')

@section('content')
<div x-data="{ mode: 'code' }">

    <div id="mode-code">
        <p class="mb-5 text-sm text-gray-500 text-center">
            Open your authenticator app and enter the 6-digit code.
        </p>

        <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-5">
            @csrf

            <x-form.input
                name="code"
                label="Authentication code"
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="000 000"
                autofocus
                required
            />

            <button type="submit"
                    class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                Verify
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            Lost your device?
            <button type="button" data-show-recovery
                    class="font-medium text-brand-600 hover:text-brand-700">Use a recovery code</button>
        </p>
    </div>


    <div id="mode-recovery" class="hidden">
        <p class="mb-5 text-sm text-gray-500 text-center">
            Enter one of your saved recovery codes.
        </p>

        <form method="POST" action="{{ route('2fa.recovery') }}" class="space-y-5">
            @csrf

            <x-form.input
                name="recovery_code"
                label="Recovery code"
                type="text"
                autocomplete="off"
                placeholder="XXXXX-XXXXX"
                autofocus
                required
            />

            <button type="submit"
                    class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                Use recovery code
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            <button type="button" data-show-code
                    class="font-medium text-brand-600 hover:text-brand-700">Use authentication code instead</button>
        </p>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    'use strict';
    $('[data-show-recovery]').on('click', function () {
        $('#mode-code').addClass('hidden');
        $('#mode-recovery').removeClass('hidden');
    });
    $('[data-show-code]').on('click', function () {
        $('#mode-recovery').addClass('hidden');
        $('#mode-code').removeClass('hidden');
    });
});
</script>
@endpush
@endsection

@extends('theme::layouts.user')

@section('breadcrumb', __('Set Up Two-Factor Authentication'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">@lang('Set up two-factor authentication')</h1>
        <p class="ud-page-sub">@lang('Scan the QR code with your authenticator app to get started.')</p>
    </div>

    <div class="ud-card w-full max-w-xl">
        <div class="space-y-6 px-2 py-2 text-center">

            <div>
                <p class="text-sm font-medium text-white/70 mb-2">@lang('1. Install an authenticator app')</p>
                <p class="text-sm text-white/50">@lang('Use Google Authenticator, Authy, or any TOTP-compatible app.')</p>
            </div>

            <div class="border-t border-white/8 pt-6">
                <p class="text-sm font-medium text-white/70 mb-3">@lang('2. Scan this QR code')</p>
                <div class="flex justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                         alt="{{ __('Two-factor QR code') }}"
                         width="200" height="200"
                         class="rounded-lg bg-white p-2" />
                </div>
                <p class="mt-3 text-xs text-white/50">
                    @lang("Can't scan? Enter this key manually:")
                    <code class="font-mono text-white/80 bg-white/8 px-1.5 py-0.5 rounded">{{ $secret }}</code>
                </p>
            </div>

            <div class="border-t border-white/8 pt-6">
                <p class="text-sm font-medium text-white/70 mb-3">@lang('3. Enter the 6-digit code from your app')</p>
                <form method="POST" action="{{ route('user.2fa.enable') }}" class="space-y-4 text-left">
                    @csrf
                    <div>
                        <label for="code" class="block text-sm font-medium text-white/70 mb-1.5">@lang('Authentication code')</label>
                        <input id="code" name="code" type="text"
                               inputmode="numeric" autocomplete="one-time-code"
                               placeholder="000 000" required
                               class="ud-input w-full" />
                        @error('code')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="ud-btn-primary w-full justify-center">
                        <x-heroicon-o-shield-check class="h-4 w-4" />
                        @lang('Confirm and enable')
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection

@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8 py-10">
    <x-page-header title="Set up two-factor authentication" />

    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 space-y-6">
        <div>
            <p class="text-sm font-medium text-gray-700 mb-2">1. Install an authenticator app</p>
            <p class="text-sm text-gray-500">Use Google Authenticator, Authy, or any TOTP-compatible app.</p>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-700 mb-3">2. Scan this QR code</p>
            <div class="flex justify-center">
                <?php echo (new \PragmaRX\Google2FAQRCode\Google2FA())->getQRCodeInline(
                    config('app.name'),
                    auth('user')->user()->email,
                    $secret
                ); ?>
            </div>
            <p class="mt-3 text-xs text-gray-400 text-center">
                Can't scan? Enter this key manually: <code class="font-mono bg-gray-100 px-1 rounded">{{ $secret }}</code>
            </p>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-700 mb-3">3. Enter the 6-digit code from your app</p>
            <form method="POST" action="{{ route('user.2fa.enable') }}" class="space-y-4">
                @csrf
                <x-form.input
                    name="code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    placeholder="000 000"
                    label="Authentication code"
                    required
                />
                <button type="submit"
                        class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Confirm and enable
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

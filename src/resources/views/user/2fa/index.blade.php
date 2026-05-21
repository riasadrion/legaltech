@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-10">
    <x-page-header title="Two-Factor Authentication" description="Add an extra layer of security to your account." />

    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6">
        @if ($enabled)
            <div class="flex items-center gap-3 mb-6">
                <x-heroicon-o-shield-check class="h-8 w-8 text-green-500" />
                <div>
                    <p class="font-semibold text-gray-900">2FA is enabled</p>
                    <p class="text-sm text-gray-500">Your account is protected with two-factor authentication.</p>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('user.2fa.setup') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Regenerate recovery codes
                </a>

                <form method="POST" action="{{ route('user.2fa.disable') }}" class="mt-4 border-t border-gray-100 pt-4">
                    @csrf
                    <p class="mb-3 text-sm text-gray-600">To disable 2FA, confirm your current password:</p>
                    <div class="flex gap-3">
                        <x-form.input name="password" type="password" placeholder="Current password" class="flex-1" />
                        <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                                data-confirm="Are you sure you want to disable two-factor authentication?">
                            Disable 2FA
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        @else
            <div class="flex items-center gap-3 mb-6">
                <x-heroicon-o-shield-exclamation class="h-8 w-8 text-yellow-500" />
                <div>
                    <p class="font-semibold text-gray-900">2FA is not enabled</p>
                    <p class="text-sm text-gray-500">Protect your account with an authenticator app.</p>
                </div>
            </div>
            <a href="{{ route('user.2fa.setup') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                Enable two-factor authentication
            </a>
        @endif
    </div>
</div>
@endsection

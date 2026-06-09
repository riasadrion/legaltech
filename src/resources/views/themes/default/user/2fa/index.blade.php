@extends('theme::layouts.user')

@section('breadcrumb', __('Two-Factor Authentication'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">@lang('Two-Factor Authentication')</h1>
        <p class="ud-page-sub">@lang('Add an extra layer of security to your account.')</p>
    </div>

    <div class="ud-card">
        @if($enabled)
            <div class="p-6 space-y-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15">
                        <x-heroicon-o-shield-check class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <p class="font-semibold text-white">@lang('2FA is Enabled')</p>
                        <p class="text-sm text-white/50">@lang('Your account is protected with two-factor authentication.')</p>
                    </div>
                    <span class="ml-auto ud-badge ud-badge-green">@lang('Active')</span>
                </div>

                <div class="border-t border-white/8 pt-5 space-y-4">
                    <div>
                        <p class="text-sm font-medium text-white/70 mb-2">@lang('Recovery Codes')</p>
                        <a href="{{ route('user.2fa.setup') }}" class="ud-btn-ghost inline-flex">
                            <x-heroicon-o-arrow-path class="h-4 w-4" />
                            @lang('Regenerate Recovery Codes')
                        </a>
                    </div>

                    <div class="border-t border-white/8 pt-4">
                        <p class="text-sm text-white/50 mb-3">@lang('To disable 2FA, confirm your current password:')</p>
                        <form method="POST" action="{{ route('user.2fa.disable') }}">
                            @csrf
                            <div class="flex gap-3">
                                <input name="password" type="password" placeholder="{{ __('Current password') }}"
                                       class="ud-input flex-1" />
                                <button type="submit" class="ud-btn-danger inline-flex shrink-0"
                                        :data-confirm="__('Are you sure you want to disable two-factor authentication?')">
                                    @lang('Disable 2FA')
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>

        @else
            <div class="p-6 space-y-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-400/15">
                        <x-heroicon-o-shield-exclamation class="h-6 w-6 text-amber-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-white">@lang('2FA is Not Enabled')</p>
                        <p class="text-sm text-white/50">@lang('Protect your account with an authenticator app.')</p>
                    </div>
                    <span class="ml-auto ud-badge ud-badge-gray">@lang('Disabled')</span>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach([
                        ['step' => '1', 'title' => __('Install App'), 'desc' => __('Google Authenticator or Authy')],
                        ['step' => '2', 'title' => __('Scan QR Code'), 'desc' => __('Link your authenticator app')],
                        ['step' => '3', 'title' => __('Enter Code'), 'desc' => __('Confirm and activate 2FA')],
                    ] as $s)
                    <div class="rounded-lg bg-white/4 border border-white/8 px-4 py-3 flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary text-xs font-bold">{{ $s['step'] }}</div>
                        <div>
                            <div class="text-sm font-medium text-white">{{ $s['title'] }}</div>
                            <div class="text-xs text-white/60 mt-0.5">{{ $s['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('user.2fa.setup') }}" class="ud-btn-primary inline-flex">
                    <x-heroicon-o-shield-check class="h-4 w-4" />
                    @lang('Enable Two-Factor Authentication')
                </a>
            </div>
        @endif
    </div>

</div>
@endsection

@extends('theme::layouts.auth')

@section('heading', __('Verify your phone number'))

@section('content')
@php($hasPhone = ! empty($user->mobile))

@if($hasPhone)

    <p class="mb-5 text-sm text-gray-500 text-center">
        @lang('We sent a 6-digit code to') <strong>{{ $user->mobile_country_code }}{{ $user->mobile }}</strong>.
    </p>

    <form method="POST" action="{{ route('phone.verify') }}" class="space-y-5">
        @csrf

        <x-form.input
            name="code"
            :label="__('Verification code')"
            type="text"
            inputmode="numeric"
            autocomplete="one-time-code"
            placeholder="000000"
            autofocus
            required
        />

        <button type="submit"
                class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
            @lang('Verify')
        </button>
    </form>

    <form method="POST" action="{{ route('phone.send') }}" class="mt-4 text-center">
        @csrf
        <input type="hidden" name="mobile_country_code" value="{{ $user->mobile_country_code }}">
        <input type="hidden" name="mobile" value="{{ $user->mobile }}">
        <button type="submit" class="text-sm text-brand-600 hover:text-brand-700 font-medium">
            @lang('Resend code')
        </button>
    </form>
@else

    <p class="mb-5 text-sm text-gray-500 text-center">
        @lang('Enter your phone number to receive a verification code.')
    </p>

    <form method="POST" action="{{ route('phone.send') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-3 gap-3">
            <x-form.input
                name="mobile_country_code"
                :label="__('Country')"
                type="text"
                placeholder="+1"
                :value="old('mobile_country_code', $user->mobile_country_code ?? '+1')"
                required
            />
            <div class="col-span-2">
                <x-form.input
                    name="mobile"
                    :label="__('Phone number')"
                    type="tel"
                    inputmode="numeric"
                    placeholder="5551234567"
                    :value="old('mobile')"
                    required
                />
            </div>
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
            @lang('Send code')
        </button>
    </form>
@endif

<form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
    @csrf
    <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">@lang('Sign out')</button>
</form>
@endsection

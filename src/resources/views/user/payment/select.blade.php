@extends('theme::layouts.user')

@section('breadcrumb', __('Add Funds'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">{{ __('Add Funds') }}</h1>
        <p class="ud-page-sub">{{ __('Choose a payment method to continue.') }}</p>
    </div>

    @if($gateways->isEmpty())
    <div class="rounded-2xl glass p-12 text-center text-white/40">
        <x-heroicon-o-credit-card class="h-10 w-10 mx-auto mb-3 opacity-30" />
        <div class="text-sm">{{ __('No payment methods are currently available.') }}</div>
        <a href="{{ route('user.payment.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-[13px] inline-flex items-center gap-1.5 mt-4">
            {{ __('Back') }}
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
        @foreach($gateways as $gateway)
        @php $logo = $gateway->logoUrl(); @endphp
        <a href="{{ route('user.payment.create', array_filter(['gateway' => $gateway->driver, 'plan_id' => request('plan_id')])) }}"
           class="rounded-2xl glass p-4 flex flex-col items-center gap-3 hover:bg-white/6 transition group text-center">

            @if($logo)
                <img src="{{ $logo }}" alt="{{ $gateway->name }}" class="w-8 h-8 rounded-lg object-contain">
            @else
                <div class="w-8 h-8 rounded-lg bg-white/6 border border-white/10 grid place-items-center">
                    <x-heroicon-o-credit-card class="h-4 w-4 text-white/50" />
                </div>
            @endif

            <div>
                <div class="text-[13px] font-medium text-white leading-tight">{{ $gateway->name }}</div>
                <div class="text-[11px] text-white/40 mt-0.5">
                    {{ __('Min') }} {{ $gateway->formatAmount($gateway->min_amount) }}
                    @if($gateway->fee_flat == 0 && $gateway->fee_percent == 0)
                        · <span class="text-lime/70">{{ __('No fee') }}</span>
                    @endif
                </div>
            </div>

        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection

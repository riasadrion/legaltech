@extends('theme::layouts.user')

@section('breadcrumb', __('Checkout'))

@section('content')
<div class="max-w-lg space-y-5">

    <div>
        <h1 class="ud-page-title">{{ __('Subscribe to :plan', ['plan' => $plan->name]) }}</h1>
        <p class="ud-page-sub">@lang('Select a payment method to complete your subscription.')</p>
    </div>


    <div class="rounded-2xl glass px-5 py-4 flex items-center justify-between">
        <div>
            <div class="text-[13px] font-medium text-white">{{ $plan->name }}</div>
            <div class="text-[11px] text-white/60 mt-0.5">{{ $plan->description }}</div>
        </div>
        <div class="text-right">
            <div class="font-display text-2xl text-white">{{ $plan->formattedPrice() }}</div>
            <div class="text-[11px] text-white/60">
                @if($plan->billing_cycle === 'monthly') @lang('/ month')
                @elseif($plan->billing_cycle === 'yearly') @lang('/ year')
                @elseif($plan->billing_cycle === 'per_contract') @lang('/ contract')
                @endif
            </div>
        </div>
    </div>


    @if($errors->any())
        <div class="rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400">
            {{ $errors->first() }}
        </div>
    @endif


    @if($gateways->isEmpty())
        <div class="rounded-2xl glass p-12 text-center text-white/40">
            <x-heroicon-o-credit-card class="h-10 w-10 mx-auto mb-3 opacity-30" />
            <div class="text-sm">@lang('No payment methods are currently available.')</div>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($gateways as $gateway)
            @php $logo = $gateway->logoUrl(); @endphp
            <form method="POST" action="{{ route('user.payment.store') }}">
                @csrf
                <input type="hidden" name="gateway_id" value="{{ $gateway->id }}">
                <input type="hidden" name="amount" value="{{ $plan->price }}">
                <input type="hidden" name="currency" value="{{ $gateway->currency_code }}">
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <button type="submit"
                        class="w-full rounded-2xl glass p-4 flex flex-col items-center gap-3 hover:bg-white/6 transition text-center">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $gateway->name }}" class="w-8 h-8 rounded-lg object-contain">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-white/6 border border-white/10 grid place-items-center">
                            <x-heroicon-o-credit-card class="h-4 w-4 text-white/50" />
                        </div>
                    @endif
                    <div>
                        <div class="text-[13px] font-medium text-white leading-tight">{{ $gateway->name }}</div>
                        @if($gateway->fee_flat == 0 && $gateway->fee_percent == 0)
                            <div class="text-[11px] text-primary/70 mt-0.5">@lang('No fee')</div>
                        @else
                            <div class="text-[11px] text-white/40 mt-0.5">
                                @if($gateway->fee_flat > 0){{ $gateway->formatAmount($gateway->fee_flat) }}@endif
                                @if($gateway->fee_flat > 0 && $gateway->fee_percent > 0) + @endif
                                @if($gateway->fee_percent > 0){{ $gateway->fee_percent }}% @lang('fee')@endif
                            </div>
                        @endif
                    </div>
                </button>
            </form>
            @endforeach
        </div>
    @endif

    <a href="{{ route('user.subscription.plans') }}" class="text-[12px] text-white/40 hover:text-white/60 transition inline-flex items-center gap-1">
        <x-heroicon-o-arrow-left class="h-3.5 w-3.5" />
        @lang('Back to plans')
    </a>

</div>
@endsection

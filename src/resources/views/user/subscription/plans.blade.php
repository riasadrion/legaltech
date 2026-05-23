@extends('theme::layouts.user')

@section('breadcrumb', __('Plans & Pricing'))

@section('content')
@php
    $grouped = $plans->groupBy('billing_cycle');
    $tabs = [
        'per_contract' => __('Per contract'),
        'monthly'      => __('Monthly'),
        'yearly'       => __('Yearly · save 20%'),
    ];
    // only show tabs that have plans
    $availableTabs = collect($tabs)->filter(fn($_, $k) => $grouped->has($k));
    $defaultTab = $availableTabs->keys()->first() ?? 'monthly';
@endphp

<div class="space-y-8">

    <div class="text-center max-w-2xl mx-auto">
        <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-3">— {{ __('Pricing') }}</div>
        <h1 class="font-display text-5xl md:text-6xl leading-[0.95] text-white">
            {{ __('Choose your') }} <span class="italic-display">{{ __('plan.') }}</span>
        </h1>
        <p class="text-white/65 mt-4">{{ __('Switch anytime — your history follows you.') }}</p>
    </div>

    {{-- Tab toggle --}}
    <div class="flex justify-center" id="plan-tabs">
        <div class="glass rounded-full p-1 flex items-center text-sm gap-0.5">
            @foreach($availableTabs as $cycle => $label)
            <button type="button"
                    data-tab="{{ $cycle }}"
                    class="plan-tab-btn rounded-full px-5 py-2 font-medium transition {{ $cycle === $defaultTab ? 'bg-lime text-ink-900' : 'text-white/65' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Plan grids per tab --}}
    @foreach($availableTabs as $cycle => $_)
    @php $cyclePlans = $grouped->get($cycle, collect()); @endphp
    <div class="plan-tab-panel grid md:grid-cols-3 gap-5 {{ $cycle !== $defaultTab ? 'hidden' : '' }}" data-panel="{{ $cycle }}">
        @foreach($cyclePlans as $plan)
        @php
            $isRecommended = $loop->iteration === 2 && count($cyclePlans) >= 2;
            $isFree = (float) $plan->price === 0.0;
        @endphp

        @if($isRecommended)
        <div class="relative rounded-3xl p-[1px] bg-gradient-to-b from-lime via-lime/40 to-transparent">
            <div class="rounded-[23px] bg-ink-900 p-7 relative overflow-hidden h-full flex flex-col">
                <div class="absolute -right-10 -top-10 w-56 h-56 glow-lime"></div>
                <div class="relative flex items-center justify-between">
                    <h3 class="font-display text-3xl text-white">{{ $plan->name }}</h3>
                    <span class="text-[10px] font-semibold uppercase tracking-widest bg-lime text-ink-900 px-2.5 py-1 rounded-full">{{ __('Recommended') }}</span>
                </div>
                @if($plan->description)
                <p class="text-white/65 text-sm mt-1 relative">{{ $plan->description }}</p>
                @endif
                <div class="mt-6 flex items-baseline gap-1 relative">
                    <span class="font-display text-6xl text-white">{{ $plan->formattedPrice() }}</span>
                    @if(!$isFree)
                    <span class="text-white/45">/ {{ $plan->billing_cycle }}</span>
                    @endif
                </div>
                @if($plan->hasTrialPeriod())
                <div class="mt-3">
                    <span class="text-[11px] font-mono text-white/50 border border-white/15 rounded-full px-3 py-1">{{ $plan->trial_days }}-{{ __('day free trial') }}</span>
                </div>
                @endif
                @if($plan->features && count($plan->features) > 0)
                <ul class="mt-6 space-y-2.5 text-sm text-white/85 relative flex-1">
                    @foreach($plan->features as $feature)
                    <li class="flex items-start gap-2">
                        <x-heroicon-s-check-circle class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                @endif
                <form method="POST" action="{{ route('user.subscription.subscribe') }}" class="mt-7 relative">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="btn-lime rounded-full px-5 py-3 text-sm w-full inline-flex items-center justify-center gap-2">
                        {{ $isFree ? __('Start free') : __('Start 14-day trial') }}
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>

        @else
        <div class="rounded-3xl glass p-7 flex flex-col">
            <div class="flex items-center justify-between">
                <h3 class="font-display text-3xl text-white">{{ $plan->name }}</h3>
                @if($isFree)
                <span class="text-[11px] font-mono text-white/45">{{ __('Custom') }}</span>
                @endif
            </div>
            @if($plan->description)
            <p class="text-white/55 text-sm mt-1">{{ $plan->description }}</p>
            @endif
            <div class="mt-6 flex items-baseline gap-1">
                @if($isFree)
                <span class="font-display text-6xl text-white">{{ __('Custom') }}</span>
                @else
                <span class="font-display text-6xl text-white">{{ $plan->formattedPrice() }}</span>
                <span class="text-white/45">/ {{ $plan->billing_cycle }}</span>
                @endif
            </div>
            @if($plan->hasTrialPeriod())
            <div class="mt-3">
                <span class="text-[11px] font-mono text-white/50 border border-white/15 rounded-full px-3 py-1">{{ $plan->trial_days }}-{{ __('day free trial') }}</span>
            </div>
            @endif
            @if($plan->features && count($plan->features) > 0)
            <ul class="mt-6 space-y-2.5 text-sm text-white/75 flex-1">
                @foreach($plan->features as $feature)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-check-circle class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
            @endif
            <form method="POST" action="{{ route('user.subscription.subscribe') }}" class="mt-7">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <button type="submit" class="btn-ghost rounded-full px-5 py-3 text-sm w-full inline-flex justify-center">
                    {{ $isFree ? __('Talk to sales') : __('Subscribe now') }}
                </button>
            </form>
        </div>
        @endif

        @endforeach
    </div>
    @endforeach

    <p class="text-center text-xs text-white/35">{{ __('All plans include full export of your data. No credit card required to start a trial.') }}</p>

</div>

@push('scripts')
<script>
$(function () {
    $(document).on('click', '.plan-tab-btn', function () {
        var tab = $(this).data('tab');
        $('.plan-tab-btn').removeClass('bg-lime text-ink-900').addClass('text-white/65');
        $(this).addClass('bg-lime text-ink-900').removeClass('text-white/65');
        $('.plan-tab-panel').addClass('hidden');
        $('.plan-tab-panel[data-panel="' + tab + '"]').removeClass('hidden');
    });
});
</script>
@endpush

@endsection

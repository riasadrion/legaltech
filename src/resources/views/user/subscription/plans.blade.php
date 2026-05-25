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
    $defaultTab = $availableTabs->has('monthly') ? 'monthly' : ($availableTabs->keys()->first() ?? 'monthly');
@endphp

<div class="space-y-8">

    @if(session('upgrade_ai'))
        <div class="rounded-2xl glass border border-lime/30 bg-lime/5 px-5 py-4 flex items-start gap-3">
            <x-heroicon-o-sparkles class="h-5 w-5 text-lime shrink-0 mt-0.5" />
            <div>
                <div class="text-white font-medium text-sm">{{ __('AI contract drafting is a Pro feature') }}</div>
                <div class="text-white/65 text-[13px] mt-0.5">{{ session('upgrade_ai') }}</div>
            </div>
        </div>
    @endif

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
        @php $isRecommended = $loop->iteration === 2 && count($cyclePlans) >= 2; @endphp
        <x-theme::plan-card
            :plan="$plan"
            :featured="$isRecommended"
            :action="['type' => 'form', 'url' => route('user.subscription.subscribe'), 'method' => 'POST']"
        />

        @endforeach
    </div>
    @endforeach

    <p class="text-center text-xs text-white/35">{{ __('All plans include full export of your data. Cancel anytime.') }}</p>

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

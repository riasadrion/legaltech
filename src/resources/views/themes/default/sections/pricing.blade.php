@php
    use App\Domain\Subscription\Models\Plan;

    $d        = $section->data ?? [];
    $eyebrow  = $d['eyebrow']         ?? '';
    $heading  = $d['heading']         ?? '';
    $subhead  = $d['subheading']      ?? '';
    $tabPer   = $d['tab_per_contract'] ?? __('Pay per contract');
    $tabMonth = $d['tab_monthly']      ?? __('Monthly · save 20%');
    $tabYear  = $d['tab_yearly']       ?? __('Yearly · save 30%');

    $symbol = setting('site.currency_symbol', '$');

    $allPlans = Plan::active()->orderBy('sort')->get();

    // Group into tiers: per_contract | monthly | yearly
    // Tier name = base name without the cycle suffix
    $tiers = [];
    foreach ($allPlans as $plan) {
        $base = trim(preg_replace('/(Monthly|Yearly|Per Contract)$/i', '', $plan->name));
        $tiers[$base][$plan->billing_cycle] = $plan;
    }
@endphp

<section id="pricing" class="relative py-28">
    <div class="mx-auto max-w-7xl px-6">

        <div class="text-center max-w-3xl mx-auto">
            @if($eyebrow)
                <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-3">— {{ __($eyebrow) }}</div>
            @endif
            @if($heading)
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
            @endif
            @if($subhead)
                <p class="text-white/65 mt-4">{{ __($subhead) }}</p>
            @endif
        </div>

        {{-- Tab toggle --}}
        <div class="flex justify-center mt-10">
            <div class="glass rounded-full p-1 flex items-center text-sm" id="pricing-tabs">
                <button data-cycle="per_contract"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition bg-lime text-ink-900">
                    {{ $tabPer }}
                </button>
                <button data-cycle="monthly"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition text-white/65">
                    {{ $tabMonth }}
                </button>
                <button data-cycle="yearly"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition text-white/65">
                    {{ $tabYear }}
                </button>
            </div>
        </div>

        {{-- Plan cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10">
            @foreach($tiers as $tierName => $cycles)
                @php
                    $plan    = $cycles['per_contract'] ?? $cycles['monthly'] ?? $cycles['yearly'] ?? null;
                    $featured = $plan?->is_featured ?? false;
                @endphp
                @if(!$plan) @continue @endif

                <div class="pricing-card-wrap {{ $featured ? 'relative rounded-3xl p-[1px] bg-gradient-to-b from-lime via-lime/40 to-transparent lift' : '' }}">
                    <div class="{{ $featured ? 'rounded-[23px] bg-ink-900 p-7 relative overflow-hidden h-full' : 'rounded-3xl glass p-7 lift' }}">
                        @if($featured)
                            <div class="absolute -right-10 -top-10 w-56 h-56 glow-lime"></div>
                        @endif

                        {{-- Header --}}
                        <div class="relative flex items-center justify-between">
                            <h3 class="font-display text-3xl">{{ __($tierName) }}</h3>
                            @if($featured)
                                <span class="text-[10px] font-semibold uppercase tracking-widest bg-lime text-ink-900 px-2.5 py-1 rounded-full">
                                    {{ __('Recommended') }}
                                </span>
                            @else
                                <span class="text-[11px] font-mono text-white/45 pricing-cycle-label">
                                    {{ __('Per contract') }}
                                </span>
                            @endif
                        </div>

                        <p class="text-sm mt-1 relative {{ $featured ? 'text-white/65' : 'text-white/55' }}">
                            {{ __($plan->description ?? '') }}
                        </p>

                        {{-- Price per cycle --}}
                        <div class="mt-6 relative" data-tier="{{ $tierName }}">
                            @foreach(['per_contract', 'monthly', 'yearly'] as $cycle)
                                @php $cp = $cycles[$cycle] ?? null; @endphp
                                <div class="pricing-price flex items-baseline gap-1 {{ $cycle !== 'per_contract' ? 'hidden' : '' }}"
                                     data-cycle="{{ $cycle }}">
                                    @if($cp && $cp->price > 0)
                                        <span class="font-display text-5xl md:text-6xl">{{ $symbol }}{{ number_format($cp->price, 0) }}</span>
                                        <span class="text-white/45">
                                            {{ $cycle === 'per_contract' ? __('/ contract') : ($cycle === 'monthly' ? __('/ month') : __('/ year')) }}
                                        </span>
                                    @else
                                        <span class="font-display text-5xl md:text-6xl">{{ __('Custom') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Features --}}
                        @if(!empty($plan->features))
                            <ul class="mt-6 space-y-2.5 text-sm relative {{ $featured ? 'text-white/85' : 'text-white/75' }}">
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start gap-2">
                                        <i class="ri-check-line text-lime mt-0.5"></i>
                                        {{ __($feature) }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- CTA --}}
                        @php
                            $ctaText = $plan->cta_text ?: __('Get started');
                            $ctaUrl  = $plan->cta_url  ?: '/register';
                        @endphp
                        <a href="{{ $ctaUrl }}"
                           class="mt-7 rounded-full px-5 py-3 text-sm w-full inline-flex items-center justify-center gap-2 relative
                                  {{ $featured ? 'btn-lime' : 'btn-ghost' }}">
                            {{ __($ctaText) }}
                            @if($featured) <i class="ri-arrow-right-line"></i> @endif
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

@push('scripts')
<script>
$(function () {
    'use strict';

    var $tabs   = $('.pricing-tab');
    var $prices = $('.pricing-price');
    var $labels = $('.pricing-cycle-label');

    var cycleLabels = {
        per_contract: '{{ __("Per contract") }}',
        monthly:      '{{ __("Per month") }}',
        yearly:       '{{ __("Per year") }}',
    };

    $tabs.on('click', function () {
        var cycle = $(this).data('cycle');

        $tabs.removeClass('bg-lime text-ink-900').addClass('text-white/65');
        $(this).addClass('bg-lime text-ink-900').removeClass('text-white/65');

        $prices.addClass('hidden');
        $prices.filter('[data-cycle="' + cycle + '"]').removeClass('hidden');

        $labels.text(cycleLabels[cycle] || '');
    });
});
</script>
@endpush

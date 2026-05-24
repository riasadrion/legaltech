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
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition text-white/65">
                    {{ $tabPer }}
                </button>
                <button data-cycle="monthly"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition bg-lime text-ink-900">
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
            @foreach($allPlans as $plan)
                <div class="pricing-card-wrap" data-tier-cycles="{{ $plan->billing_cycle }}">
                    <x-theme::plan-card :plan="$plan" />
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
    var $cards  = $('.pricing-card-wrap');

    var cycleLabels = {
        per_contract: '{{ __("Per contract") }}',
        monthly:      '{{ __("Per month") }}',
        yearly:       '{{ __("Per year") }}',
    };

    function applyCycle(cycle) {
        $tabs.removeClass('bg-lime text-ink-900').addClass('text-white/65');
        $tabs.filter('[data-cycle="' + cycle + '"]').addClass('bg-lime text-ink-900').removeClass('text-white/65');

        $prices.addClass('hidden');
        $prices.filter('[data-cycle="' + cycle + '"]').removeClass('hidden');

        $labels.text(cycleLabels[cycle] || '');

        // Hide cards that don't include the active cycle
        $cards.each(function () {
            var cycles = ($(this).data('tier-cycles') || '').toString().split(',');
            $(this).toggleClass('hidden', cycles.indexOf(cycle) === -1);
        });
    }

    $tabs.on('click', function () {
        applyCycle($(this).data('cycle'));
    });

    applyCycle('monthly');
});
</script>
@endpush

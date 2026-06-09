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

<section id="pricing" class="relative py-28 {{ $sectionBg ?? 'bg-cream-50' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-7xl px-6">

        <x-theme::section-header
            :eyebrow="$eyebrow"
            :heading="$heading"
            :subhead="$subhead"
            align="center"
            class="max-w-3xl mx-auto"
            data-reveal
        />


        <div class="flex justify-center mt-10">
            <div class="glass-cream rounded-full p-1 flex items-center text-sm" id="pricing-tabs">
                <button data-cycle="per_contract"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition text-ink-600">
                    {{ $tabPer }}
                </button>
                <button data-cycle="monthly"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition bg-primary text-white">
                    {{ $tabMonth }}
                </button>
                <button data-cycle="yearly"
                        class="pricing-tab rounded-full px-5 py-2 font-medium transition text-ink-600">
                    {{ $tabYear }}
                </button>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10" data-reveal data-reveal-delay="100">
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
        per_contract: '@lang("Per contract")',
        monthly:      '@lang("Per month")',
        yearly:       '@lang("Per year")',
    };

    function applyCycle(cycle) {
        $tabs.removeClass('bg-primary text-white').addClass('text-ink-600');
        $tabs.filter('[data-cycle="' + cycle + '"]').addClass('bg-primary text-white').removeClass('text-ink-600');

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

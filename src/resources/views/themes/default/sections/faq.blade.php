@php
    $d       = $section->data ?? [];
    $eyebrow = $d['eyebrow'] ?? '';
    $heading = $d['heading'] ?? '';
    $items   = array_values(array_filter($d['items'] ?? [], fn($i) => !empty($i['question'])));
@endphp

<section id="faq" class="relative py-28 {{ $sectionBg ?? 'bg-cream-100' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-5xl px-6">

        <x-theme::section-header
            :eyebrow="$eyebrow"
            :heading="$heading"
            align="center"
            size="xl"
            class="max-w-2xl mx-auto mb-12"
            data-reveal
        />

        @if(!empty($items))
        <div class="divide-y divide-ink-900/10 rounded-3xl glass-cream overflow-hidden" data-reveal data-reveal-delay="100">
            @foreach($items as $item)
                <div class="faq-item group p-6 transition-colors duration-300">
                    <button type="button"
                            class="faq-trigger w-full flex items-center justify-between gap-6 cursor-pointer text-left"
                            aria-expanded="false">
                        <span class="font-display text-xl md:text-2xl">{{ __($item['question']) }}</span>
                        <span class="acc-icon w-9 h-9 rounded-full glass-cream grid place-items-center shrink-0">
                            <x-heroicon-o-plus class="h-4 w-4 faq-icon transition-colors duration-300" />
                        </span>
                    </button>
                    <div class="faq-panel grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-out">
                        <div class="overflow-hidden">
                            <p class="mt-4 text-ink-600 faq-answer transition-colors duration-300 max-w-3xl">
                                {{ __($item['answer'] ?? '') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
$(function () {
    'use strict';
    $('.faq-item').each(function () {
        var $item    = $(this);
        var $trigger = $item.find('.faq-trigger');
        var $panel   = $item.find('.faq-panel');
        var $icon    = $item.find('.acc-icon');

        $trigger.on('click', function () {
            var willOpen = $trigger.attr('aria-expanded') !== 'true';
            var $answer  = $item.find('.faq-answer');
            var $iconSvg = $item.find('.faq-icon');

            $trigger.attr('aria-expanded', String(willOpen));

            $panel.toggleClass('grid-rows-[1fr]', willOpen).toggleClass('grid-rows-[0fr]', !willOpen);
            $icon.toggleClass('rotate-180', willOpen);
            $icon.toggleClass('acc-icon-active', willOpen);
            $iconSvg.toggleClass('text-white', willOpen);
            $item.toggleClass('bg-ink-900', willOpen).toggleClass('text-white', willOpen);
            $answer.toggleClass('text-white/65', willOpen).toggleClass('text-ink-600', !willOpen);
        });
    });
});
</script>
@endpush

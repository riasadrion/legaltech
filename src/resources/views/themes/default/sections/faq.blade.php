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
            @foreach($items as $i => $item)
                @php $isOpen = ($i === 1); @endphp
                <div class="faq-item group p-6 transition-colors duration-300 {{ $isOpen ? 'bg-ink-900 text-white' : '' }}">
                    <button type="button"
                            class="faq-trigger w-full flex items-center justify-between gap-6 cursor-pointer text-left"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                        <span class="font-display text-xl md:text-2xl">{{ __($item['question']) }}</span>
                        <span class="acc-icon w-9 h-9 rounded-full glass-cream grid place-items-center shrink-0 {{ $isOpen ? 'rotate-180 acc-icon-active' : '' }}">
                            <x-heroicon-o-plus class="h-4 w-4 faq-icon transition-colors duration-300 {{ $isOpen ? 'text-white' : '' }}" />
                        </span>
                    </button>
                    <div class="faq-panel grid {{ $isOpen ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' }} transition-[grid-template-rows] duration-300 ease-out">
                        <div class="overflow-hidden">
                            <p class="mt-4 faq-answer transition-colors duration-300 max-w-3xl {{ $isOpen ? 'text-white/65' : 'text-ink-600' }}">
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

            // Close all items first
            $('.faq-item').each(function () {
                var $other        = $(this);
                var $otherTrigger = $other.find('.faq-trigger');
                var $otherPanel   = $other.find('.faq-panel');
                var $otherIcon    = $other.find('.acc-icon');
                var $otherIconSvg = $other.find('.faq-icon');
                var $otherAnswer  = $other.find('.faq-answer');

                $otherTrigger.attr('aria-expanded', 'false');
                $otherPanel.removeClass('grid-rows-[1fr]').addClass('grid-rows-[0fr]');
                $otherIcon.removeClass('rotate-180 acc-icon-active');
                $otherIconSvg.removeClass('text-white');
                $other.removeClass('bg-ink-900 text-white');
                $otherAnswer.removeClass('text-white/65').addClass('text-ink-600');
            });

            if (willOpen) {
                var $answer  = $item.find('.faq-answer');
                var $iconSvg = $item.find('.faq-icon');

                $trigger.attr('aria-expanded', 'true');
                $panel.removeClass('grid-rows-[0fr]').addClass('grid-rows-[1fr]');
                $icon.addClass('rotate-180 acc-icon-active');
                $iconSvg.addClass('text-white');
                $item.addClass('bg-ink-900 text-white');
                $answer.removeClass('text-ink-600').addClass('text-white/65');
            }
        });
    });
});
</script>
@endpush

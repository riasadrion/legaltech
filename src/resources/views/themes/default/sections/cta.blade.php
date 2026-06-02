@php
    $d        = $section->data ?? [];
    $pill     = $d['pill_text']      ?? '';
    $line1    = $d['heading_line_1'] ?? '';
    $line2    = $d['heading_line_2'] ?? '';
    $subhead  = $d['subheading']     ?? '';
    $pText    = $d['primary_text']   ?? '';
    $pUrl     = link_url($d['primary_url'] ?? '#');
    $sText    = $d['secondary_text'] ?? '';
    $sUrl     = link_url($d['secondary_url'] ?? '#');
@endphp

<section class="relative pt-28 pb-40 overflow-hidden">
    <div class="absolute inset-0 bg-grid radial-fade pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] glow-lime opacity-80"></div>

    <div class="relative mx-auto max-w-5xl px-6 text-center" data-reveal>

        @if($pill)
            <div class="inline-flex items-center gap-2 rounded-full glass px-3 py-1.5 text-xs text-white/75">
                <span class="w-2 h-2 rounded-full bg-lime pulse-dot"></span>
                {{ __($pill) }}
            </div>
        @endif

        @if($line1 || $line2)
            <h2 class="font-display text-5xl md:text-6xl lg:text-8xl leading-[0.95] mt-6">
                @if($line1)
                    {{ __($line1) }}<br />
                @endif
                @if($line2)
                    <span class="italic-display lime-grad">{{ __($line2) }}</span>
                @endif
            </h2>
        @endif

        @if($subhead)
            <p class="text-white/65 text-lg mt-6 max-w-xl mx-auto">{{ __($subhead) }}</p>
        @endif

        @if($pText || $sText)
        <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
            @if($pText)
                <a href="{{ $pUrl }}" class="btn-lime rounded-full px-7 py-4 text-[15px] flex items-center gap-2">
                    {{ __($pText) }}
                    <x-heroicon-o-arrow-up-right class="h-4 w-4" />
                </a>
            @endif
            @if($sText)
                <a href="{{ $sUrl }}" class="btn-ghost rounded-full px-7 py-4 text-[15px] flex items-center gap-2">
                    <x-heroicon-o-calendar class="h-4 w-4" />
                    {{ __($sText) }}
                </a>
            @endif
        </div>
        @endif

    </div>
</section>

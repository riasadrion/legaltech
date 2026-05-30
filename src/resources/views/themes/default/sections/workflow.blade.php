@php
    $d = $section->data ?? [];
    $eyebrow  = $d['eyebrow']  ?? '';
    $heading  = $d['heading']  ?? '';
    $cta_text = $d['cta_text'] ?? '';
    $cta_url  = $d['cta_url']  ?? '';
    $steps    = array_values(array_filter($d['steps'] ?? [], fn($s) => !empty($s['title'])));
@endphp

<section id="workflow" class="relative py-28 bg-cream-100 text-ink-900">
    <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
    <div class="relative mx-auto max-w-7xl px-6">

        <div class="flex flex-wrap items-end justify-between gap-6 mb-16">
            <div>
                @if($eyebrow)
                    <div class="text-[11px] font-mono uppercase tracking-[.25em] text-ink-500 mb-3">— {{ __($eyebrow) }}</div>
                @endif
                @if($heading)
                    <h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
                @endif
            </div>
            @if($cta_text && $cta_url)
                <a href="{{ $cta_url }}"
                   class="hidden md:inline-flex items-center gap-2 rounded-full bg-ink-900 text-white px-5 py-3 text-sm font-medium">
                    {{ __($cta_text) }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                </a>
            @endif
        </div>

        @if(!empty($steps))
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($steps as $i => $step)
                @php $last = $i === count($steps) - 1; @endphp
                <div class="relative rounded-3xl p-7 shadow-card lift overflow-hidden
                            {{ $last ? 'bg-ink-900 text-white' : 'bg-white' }}">
                    @if($last)
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 glow-lime"></div>
                    @endif
                    <div class="relative flex items-center justify-between">
                        <span class="font-display text-5xl {{ $last ? '' : 'text-ink-900' }}">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="text-[11px] font-mono uppercase tracking-widest
                                     {{ $last ? 'text-lime' : 'text-ink-400' }}">
                            {{ __($step['tag'] ?? '') }}
                        </span>
                    </div>
                    <h3 class="font-display text-2xl mt-6 relative">{{ __($step['title']) }}</h3>
                    <p class="text-sm mt-2 leading-relaxed relative
                              {{ $last ? 'text-white/65' : 'text-ink-500' }}">
                        {{ __($step['body'] ?? '') }}
                    </p>
                </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

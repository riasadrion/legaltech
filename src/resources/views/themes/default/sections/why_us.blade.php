@php
    $d     = $section->data ?? [];
    $eyebrow = $d['eyebrow'] ?? '';
    $heading = $d['heading'] ?? '';
    $body    = $d['body']    ?? '';
    $stats   = array_values(array_filter($d['stats'] ?? [], fn($s) => !empty($s['value'])));
    $cards   = array_values(array_filter($d['cards'] ?? [], fn($c) => !empty($c['title'])));
@endphp

<section class="relative py-28 bg-cream-100 text-ink-900">
    <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
    <div class="relative mx-auto max-w-7xl px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

        {{-- Left --}}
        <div>
            @if($eyebrow)
                <div class="text-[11px] font-mono uppercase tracking-[.25em] text-ink-500 mb-3">— {{ __($eyebrow) }}</div>
            @endif
            @if($heading)
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
            @endif
            @if($body)
                <p class="mt-5 text-ink-500 text-lg max-w-md">{{ __($body) }}</p>
            @endif

            @if(!empty($stats))
                <dl class="mt-10 grid grid-cols-2 gap-6">
                    @foreach($stats as $stat)
                        <div>
                            <dt class="font-display text-5xl"><?php echo __($stat['value']); ?></dt>
                            <dd class="text-sm text-ink-500 mt-1">{{ __($stat['label'] ?? '') }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        {{-- Right: feature cards --}}
        @if(!empty($cards))
            <div class="grid grid-cols-2 gap-3">
                @foreach($cards as $card)
                    @php $dark = strtolower($card['dark'] ?? '') === 'yes'; @endphp
                    <div class="rounded-3xl shadow-card p-6 lift {{ $dark ? 'bg-ink-900 text-white' : 'bg-white' }}">
                        @if(!empty($card['icon']))
                            <i class="{{ $card['icon'] }} text-2xl {{ $dark ? 'text-lime' : 'text-ink-900' }}"></i>
                        @endif
                        <h4 class="font-display text-xl mt-3">{{ __($card['title']) }}</h4>
                        <p class="text-sm mt-1 {{ $dark ? 'text-white/65' : 'text-ink-500' }}">
                            {{ __($card['body'] ?? '') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>

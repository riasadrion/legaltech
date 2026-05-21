@php
    $d       = $section->data ?? [];
    $eyebrow = $d['eyebrow']     ?? '';
    $heading = $d['heading']     ?? '';
    $rating  = $d['rating_text'] ?? '';
    $items   = array_values(array_filter($d['testimonials'] ?? [], fn($t) => !empty($t['quote'])));

    $featured = $items[0]      ?? null;
    $side     = array_slice($items, 1, 2);
    $bottom   = array_slice($items, 3);

    $avatarColors = ['bg-gradient-to-br from-lime to-lime-deep text-ink-900', 'bg-cream-100 text-ink-900', 'bg-cream-100 text-ink-900', 'bg-lime/40 text-ink-900', 'bg-violet-500/40 text-white', 'bg-cream-100 text-ink-900'];
@endphp

<section class="relative py-28">
    <div class="mx-auto max-w-7xl px-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <div>
                @if($eyebrow)
                    <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-3">— {{ __($eyebrow) }}</div>
                @endif
                @if($heading)
                    <h2 class="font-display text-5xl md:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
                @endif
            </div>
            @if($rating)
                <div class="flex items-center gap-2 text-sm text-white/60">
                    <i class="ri-star-fill text-lime"></i>
                    <span>{{ __($rating) }}</span>
                </div>
            @endif
        </div>

        @if(!empty($items))

        {{-- Top: featured (2/3) + side stack (1/3) --}}
        <div class="grid md:grid-cols-3 gap-4 items-stretch">

            {{-- Featured — no quote icon, large display text --}}
            @if($featured)
            <div class="md:col-span-2 rounded-3xl glass p-9 lift relative overflow-hidden flex flex-col">
                <div class="absolute -right-10 -top-10 w-60 h-60 glow-lime opacity-50"></div>
                <i class="ri-double-quotes-l text-5xl text-lime relative"></i>
                <p class="font-display text-3xl md:text-4xl leading-tight mt-4 relative flex-1">
                    {{ __($featured['quote']) }}
                </p>
                <div class="mt-8 flex items-center gap-3 relative">
                    <div class="w-12 h-12 rounded-full {{ $avatarColors[0] }} grid place-items-center font-display text-xl shrink-0">
                        {{ mb_strtoupper(mb_substr($featured['name'] ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium">{{ __($featured['name'] ?? '') }}</div>
                        <div class="text-sm text-white/55">{{ __($featured['role'] ?? '') }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Side stack — with quote icon --}}
            <div class="flex flex-col gap-4">
                @foreach($side as $i => $t)
                <div class="flex-1 rounded-3xl glass p-6 lift flex flex-col">
                    <i class="ri-double-quotes-l text-3xl text-lime"></i>
                    <p class="mt-2 text-[15px] leading-snug flex-1">"{{ __($t['quote']) }}"</p>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $avatarColors[$i + 1] ?? 'bg-cream-100 text-ink-900' }} grid place-items-center font-display shrink-0">
                            {{ mb_strtoupper(mb_substr($t['name'] ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm">{{ __($t['name'] ?? '') }}</div>
                            <div class="text-xs text-white/50">{{ __($t['role'] ?? '') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom row — no quote icon --}}
        @if(!empty($bottom))
        <div class="grid md:grid-cols-3 gap-4 mt-4">
            @foreach($bottom as $i => $t)
            <div class="rounded-3xl glass p-6 lift flex flex-col">
                <p class="text-[15px] leading-snug flex-1">"{{ __($t['quote']) }}"</p>
                <div class="mt-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $avatarColors[$i + 3] ?? 'bg-cream-100 text-ink-900' }} grid place-items-center font-display shrink-0">
                        {{ mb_strtoupper(mb_substr($t['name'] ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm">{{ __($t['name'] ?? '') }}</div>
                        <div class="text-xs text-white/50">{{ __($t['role'] ?? '') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @endif
    </div>
</section>

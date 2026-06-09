@php
    $d       = $section->data ?? [];
    $eyebrow = $d['eyebrow']     ?? '';
    $heading = $d['heading']     ?? '';
    $rating  = $d['rating_text'] ?? '';
    $items   = array_values(array_filter($d['testimonials'] ?? [], fn($t) => !empty($t['quote'])));

    $featured = $items[0]      ?? null;
    $side     = array_slice($items, 1, 2);
    $bottom   = array_slice($items, 3);

    $avatarColors = ['bg-gradient-to-br from-primary to-primary-deep text-ink-900', 'bg-cream-100 text-ink-900', 'bg-cream-100 text-ink-900', 'bg-primary/40 text-ink-900', 'bg-violet-500/40 text-white', 'bg-cream-100 text-ink-900'];
@endphp

<section class="relative py-28 {{ $sectionBg ?? 'bg-cream-50' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-7xl px-6">


        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <x-theme::section-header :eyebrow="$eyebrow" :heading="$heading" />
            @if($rating)
                <div class="flex items-center gap-2 text-sm text-ink-600">
                    <x-heroicon-s-star class="h-4 w-4 text-primary" />
                    <span>{{ __($rating) }}</span>
                </div>
            @endif
        </div>

        @if(!empty($items))


        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">


            @if($featured)
            <div class="md:col-span-2 rounded-3xl bg-ink-900 text-white p-9 lift relative overflow-hidden flex flex-col">
                <div class="absolute -right-10 -top-10 w-60 h-60 glow-primary opacity-60"></div>
                <x-icon-quote class="h-12 w-12 text-primary relative" />
                <p class="font-display text-3xl md:text-4xl leading-tight mt-4 relative flex-1">
                    {{ __($featured['quote']) }}
                </p>
                <div class="mt-8 flex items-center gap-3 relative">
                    <div class="w-12 h-12 rounded-full {{ $avatarColors[0] }} grid place-items-center font-display text-xl shrink-0">
                        {{ mb_strtoupper(mb_substr($featured['name'] ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-white">{{ __($featured['name'] ?? '') }}</div>
                        <div class="text-sm text-white/55">{{ __($featured['role'] ?? '') }}</div>
                    </div>
                </div>
            </div>
            @endif


            <div class="flex flex-col gap-4">
                @foreach($side as $i => $t)
                <div class="flex-1 rounded-3xl glass-cream p-6 lift flex flex-col">
                    <x-icon-quote class="h-7 w-7 text-primary" />
                    <p class="mt-2 text-[15px] leading-snug flex-1">"{{ __($t['quote']) }}"</p>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $avatarColors[$i + 1] ?? 'bg-cream-100 text-ink-900' }} grid place-items-center font-display shrink-0">
                            {{ mb_strtoupper(mb_substr($t['name'] ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm">{{ __($t['name'] ?? '') }}</div>
                            <div class="text-xs text-ink-500">{{ __($t['role'] ?? '') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>


        @if(!empty($bottom))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @foreach($bottom as $i => $t)
            <div class="rounded-3xl glass-cream p-6 lift flex flex-col">
                <p class="text-[15px] leading-snug flex-1">"{{ __($t['quote']) }}"</p>
                <div class="mt-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $avatarColors[$i + 3] ?? 'bg-cream-100 text-ink-900' }} grid place-items-center font-display shrink-0">
                        {{ mb_strtoupper(mb_substr($t['name'] ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm">{{ __($t['name'] ?? '') }}</div>
                        <div class="text-xs text-ink-500">{{ __($t['role'] ?? '') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @endif
    </div>
</section>

@php
    $d       = $section->data ?? [];
    $eyebrow = $d['eyebrow'] ?? '';
    $heading = $d['heading'] ?? '';
    $items   = array_values(array_filter($d['items'] ?? [], fn($i) => !empty($i['question'])));
@endphp

<section id="faq" class="relative py-28">
    <div class="mx-auto max-w-5xl px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            @if($eyebrow)
                <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-3">— {{ __($eyebrow) }}</div>
            @endif
            @if($heading)
                <h2 class="font-display text-5xl md:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
            @endif
        </div>

        @if(!empty($items))
        <div class="divide-y divide-white/8 rounded-3xl glass overflow-hidden">
            @foreach($items as $item)
                <details class="group p-6">
                    <summary class="flex items-center justify-between gap-6 cursor-pointer list-none">
                        <span class="font-display text-xl md:text-2xl">{{ __($item['question']) }}</span>
                        <span class="acc-icon w-9 h-9 rounded-full glass grid place-items-center shrink-0 transition-transform group-open:rotate-45">
                            <i class="ri-add-line"></i>
                        </span>
                    </summary>
                    <p class="mt-4 text-white/65 max-w-3xl">
                        {{ __($item['answer'] ?? '') }}
                    </p>
                </details>
            @endforeach
        </div>
        @endif

    </div>
</section>

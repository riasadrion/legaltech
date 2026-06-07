@php
    $d = $section->getLocaleData();
    $heading = $d['heading'] ?? '';
    $logos = array_values(array_filter($d['logos'] ?? [], fn($l) => !empty($l['url'])));
@endphp
<section class="relative py-14 border-y border-white/8 bg-ink-900/70 overflow-hidden">
    <div class="mx-auto max-w-7xl px-6">
        @if($heading)
            <h2 class="text-center text-sm font-mono uppercase tracking-[.25em] text-white/55 font-normal">
                {{ __($heading) }}
            </h2>
        @else
            <h2 class="sr-only">@lang('Trusted by')</h2>
        @endif
    </div>

    @if(!empty($logos))
    <div class="mt-8 relative">
        <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-ink-900 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-ink-900 to-transparent z-10 pointer-events-none"></div>
        <div class="flex marquee gap-16 whitespace-nowrap text-white/55">
            <div class="flex gap-16 items-center shrink-0">
                @foreach($logos as $logo)
                    <img src="{{ upload_url($logo['url']) }}"
                         alt="{{ $logo['alt'] ?? '' }}"
                         class="h-7 w-auto opacity-70 hover:opacity-100 transition">
                @endforeach
            </div>
            <div class="flex gap-16 items-center shrink-0" aria-hidden="true">
                @foreach($logos as $logo)
                    <img src="{{ upload_url($logo['url']) }}"
                         alt=""
                         class="h-7 w-auto opacity-70">
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>

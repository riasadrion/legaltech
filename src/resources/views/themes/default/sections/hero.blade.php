@php
    $d = $section->getLocaleData();
    $heroTyperId = 'hero-typer-' . $section->id;
@endphp
<section class="relative pt-36 pb-28 overflow-hidden bg-cream-50">

    <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
    <div class="absolute -top-24 right-[8%] w-[520px] h-[360px] glow-primary opacity-25"></div>
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[700px] h-[260px] glow-primary opacity-50"></div>

    <div class="relative mx-auto max-w-7xl px-6">


        @if(!empty($d['pill_text']))
            <div class="flex justify-center px-4" data-reveal>
                <a href="{{ link_url($d['pill_url'] ?? '#') }}"
                   class="group inline-flex items-center gap-2 sm:gap-3 rounded-full glass-cream px-2 py-1.5 pr-3 sm:pr-4 text-xs sm:text-sm text-ink-700 max-w-full">
                    @if(!empty($d['pill_label']))
                        <span class="shrink-0 rounded-full bg-primary text-white font-semibold text-xs px-2.5 py-1">
                            {{ __($d['pill_label']) }}
                        </span>
                    @endif
                    <span class="whitespace-nowrap overflow-hidden text-ellipsis">{{ __($d['pill_text']) }}</span>
                    <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0 transition group-hover:translate-x-0.5" />
                </a>
            </div>
        @endif


        <h1 class="mt-8 text-center font-display text-[60px] leading-[0.93] sm:text-[80px] md:text-[104px] tracking-tight text-ink-950" data-reveal data-reveal-delay="100">
            @if(!empty($d['heading_line_1']))
                <span>{{ __($d['heading_line_1']) }} </span>
            @endif
            @if(!empty($d['heading_italic_1']))
                <span class="italic-display primary-grad">{{ __($d['heading_italic_1']) }}</span>
            @endif
            <br class="hidden sm:block">
            @if(!empty($d['heading_line_2']))
                <span>{{ __($d['heading_line_2']) }} </span>
            @endif
            @if(!empty($d['heading_italic_2']))
                <span class="italic-display text-ink-950">{{ __($d['heading_italic_2']) }}</span>
            @endif
        </h1>


        @if(!empty($d['subheading']))
            <p class="mx-auto mt-6 max-w-xl text-center text-lg text-ink-500" data-reveal data-reveal-delay="200">
                {{ __($d['subheading']) }}
                @if(!empty($d['subheading_emphasis']))
                    <span class="text-primary-deep font-semibold">{{ __($d['subheading_emphasis']) }}</span>
                @endif
            </p>
        @endif


        @if(!empty($d['primary_text']) || !empty($d['secondary_text']))
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                @if(!empty($d['primary_text']))
                    <a href="{{ link_url($d['primary_url'] ?? '#') }}"
                       class="btn-primary no-shadow text-on-primary-white rounded-full px-6 py-3.5 text-[15px] flex items-center gap-2">
                        {{ __($d['primary_text']) }}
                        <x-heroicon-s-sparkles class="h-4 w-4" />
                    </a>
                @endif
                @if(!empty($d['secondary_text']))
                    <a href="#workflow"
                       class="rounded-full px-6 py-3.5 text-[15px] flex items-center gap-2 border border-ink-900/10 text-ink-700 bg-white/60 backdrop-blur hover:bg-white/80 transition">
                        <x-heroicon-o-chevron-double-down class="h-5 w-5" />
                        {{ __($d['secondary_text']) }}
                    </a>
                @endif
            </div>
        @endif


        <div class="relative mt-14 floaty">
            <div class="relative mx-auto max-w-5xl rounded-[28px] glass-cream p-2.5 shadow-[var(--shadow-glow)]">
                <div class="rounded-[22px] bg-white border border-ink-900/8 overflow-hidden">


                    <div class="flex items-center gap-4 px-4 py-3 border-b border-ink-900/8 bg-cream-100/70">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-ink-900/15"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-ink-900/15"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-ink-900/15"></span>
                        </div>
                        <div class="flex-1 flex justify-center px-4">
                            <div class="text-[11px] font-mono text-ink-400 flex items-center gap-1.5 bg-ink-900/5 rounded-full px-3 py-1 w-full">
                                <x-heroicon-s-lock-closed class="h-3 w-3 shrink-0" />
                                {{ parse_url(config('app.url'), PHP_URL_HOST) }} / contracts / msa-acme-2026
                            </div>
                        </div>
                    </div>


                    <div class="grid grid-cols-12 min-h-[420px]">


                        <aside class="col-span-3 hidden md:flex flex-col border-r border-ink-900/8 p-4 gap-0.5">
                            <div class="flex items-center gap-2 px-2 py-1.5 mb-3">
                                <span class="w-7 h-7 rounded-full bg-primary grid place-items-center">
                                    <x-heroicon-s-pencil class="h-3.5 w-3.5 text-white" />
                                </span>
                                <span class="font-display text-lg text-ink-900">{{ setting('site.name', config('app.name')) }}</span>
                            </div>
                            <div class="flex items-center gap-2 rounded-lg bg-ink-900/4 border border-ink-900/8 px-2.5 py-2 mb-3">
                                <span class="w-5 h-5 rounded-md bg-primary grid place-items-center text-[9px] font-bold text-white">N</span>
                                <div>
                                    <div class="text-[12px] leading-none text-ink-900">Northbeam</div>
                                    <div class="text-[9px] font-mono text-ink-400 uppercase tracking-widest mt-0.5">Studio</div>
                                </div>
                            </div>
                            <div class="text-[9px] font-mono uppercase tracking-[.15em] text-ink-400/70 px-2 mb-1">Workspace</div>
                            <div class="flex items-center gap-2 px-2.5 py-1.5 text-[12px] text-ink-500">
                                <x-heroicon-o-home class="h-3.5 w-3.5" /> Dashboard
                            </div>
                            <div class="flex items-center gap-2 px-2.5 py-1.5 text-[12px] text-ink-500">
                                <x-heroicon-o-sparkles class="h-3.5 w-3.5 text-primary-deep" /> AI Contract
                            </div>
                            <div class="flex items-center gap-2 rounded-lg bg-ink-900/6 border border-ink-900/10 px-2.5 py-1.5 text-[12px] text-ink-900">
                                <x-heroicon-o-document-text class="h-3.5 w-3.5 text-primary-deep" /> Contracts
                                <span class="ml-auto text-[10px] font-mono text-ink-400">42</span>
                            </div>
                            <div class="flex items-center gap-2 px-2.5 py-1.5 text-[12px] text-ink-500">
                                <x-heroicon-o-square-3-stack-3d class="h-3.5 w-3.5" /> Subscription
                            </div>
                            <div class="flex items-center gap-2 px-2.5 py-1.5 text-[12px] text-ink-500">
                                <x-heroicon-o-credit-card class="h-3.5 w-3.5" /> Payments
                            </div>
                        </aside>


                        <div class="col-span-12 md:col-span-9 p-5 md:p-6">

                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="text-[10px] font-mono text-ink-400 uppercase tracking-widest">MSA · v3.draft</div>
                                    <p class="font-display text-xl mt-0.5 text-ink-900">Master Services Agreement — Acme Co.</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-4">
                                    <span class="text-[10px] rounded-full bg-primary/20 text-primary-deep px-2.5 py-1 border border-primary/30">AI · drafting</span>
                                    <button type="button" class="text-[11px] rounded-lg bg-ink-900/5 border border-ink-900/10 px-2.5 py-1.5 text-ink-700">Share</button>
                                </div>
                            </div>


                            <div class="rounded-xl bg-cream-100/70 border border-ink-900/8 p-4 hero-typer-wrap">
                                <div class="flex items-center gap-2 text-[10px] font-mono text-ink-400 mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    {{ setting('site.name', config('app.name')) }} · GENERATING CLAUSE 4.2
                                </div>
                                <p id="{{ $heroTyperId }}" class="text-[12.5px] font-mono text-ink-700 caret leading-relaxed hero-typer-text"></p>
                            </div>

                            <div class="mt-4 grid grid-cols-12 gap-3">

                                <div class="col-span-12 md:col-span-7 rounded-xl bg-white border border-ink-900/8 text-ink-900 p-5">
                                    <div class="text-[9px] font-mono uppercase tracking-widest text-ink-400">Schedule A · Scope</div>
                                    <div class="font-display text-lg mt-1.5">1. Definitions</div>
                                    <p class="text-[11px] leading-relaxed text-ink-600 mt-1.5">
                                        "Services" means development and maintenance of the platform described in
                                        <span class="bg-primary/60 rounded px-0.5">Schedule B</span>, including deliverables in a Statement of Work…
                                    </p>
                                    <div class="font-display text-lg mt-3">2. Term &amp; Termination</div>
                                    <p class="text-[11px] leading-relaxed text-ink-600 mt-1.5">
                                        This Agreement continues for an initial period of
                                        <span class="underline decoration-primary decoration-2 underline-offset-2">twenty-four (24) months</span>…
                                    </p>
                                    <div class="mt-4 grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="text-[9px] font-mono uppercase text-ink-400">Client</div>
                                            <svg viewBox="0 0 180 40" class="h-8 w-full mt-0.5">
                                                <path class="sig-path" d="M4 28 C18 5,30 38,48 20 S84 4,102 24 S140 40,176 12"
                                                      fill="none" stroke="#0B0B0F" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            <div class="text-[9px] text-ink-500">Elena Martí · 12 May 2026</div>
                                        </div>
                                        <div>
                                            <div class="text-[9px] font-mono uppercase text-ink-400">Provider</div>
                                            <div class="h-8 border-b border-dashed border-ink-300 flex items-end">
                                                <span class="text-[9px] text-ink-400 font-mono pb-0.5">awaiting…</span>
                                            </div>
                                            <div class="text-[9px] text-ink-500 mt-0.5">Julien Rouvier · pending</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-5 flex flex-col gap-2.5">
                                    <div class="rounded-xl border border-ink-900/8 bg-cream-100/60 p-4">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="text-[10px] font-mono text-ink-400 uppercase tracking-widest">Risk score</div>
                                            <div class="text-[10px] text-primary-deep font-semibold">LOW</div>
                                        </div>
                                        <div class="flex items-end gap-3">
                                            <div class="font-display text-4xl text-ink-900">12<span class="text-ink-400 text-base">/100</span></div>
                                            <div class="flex-1 h-1.5 rounded-full bg-ink-900/8 overflow-hidden mb-2">
                                                <div class="h-full w-[12%] bg-primary"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-primary/40 bg-primary/10 p-4 flex-1">
                                        <div class="text-[10px] font-mono text-primary-deep uppercase tracking-widest mb-1.5">Suggested edit</div>
                                        <p class="text-[12px] text-ink-700 leading-snug">Add a 30-day cure period to §8 termination for cause.</p>
                                        <div class="mt-3 flex gap-2">
                                            <button type="button" class="text-[11px] rounded-full bg-primary text-white font-semibold px-3 py-1">Accept</button>
                                            <button type="button" class="text-[11px] rounded-full bg-ink-900/8 border border-ink-900/10 px-3 py-1 text-ink-700">Reject</button>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-ink-900/8 bg-cream-100/60 p-3.5">
                                        <div class="text-[10px] font-mono text-ink-400 uppercase tracking-widest mb-2">Signatures</div>
                                        <div class="flex items-center gap-2.5 mb-2">
                                            <div class="w-6 h-6 rounded-full bg-primary grid place-items-center text-[10px] font-bold text-white">EM</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[12px] text-ink-900">Elena Martí</div>
                                                <div class="text-[10px] text-ink-400">Signed · 11:42 GMT</div>
                                            </div>
                                            <x-heroicon-s-check-circle class="h-4 w-4 text-primary-deep" />
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-6 h-6 rounded-full bg-ink-900/10 grid place-items-center text-[10px] text-ink-500">JR</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[12px] text-ink-900">Julien Rouvier</div>
                                                <div class="text-[10px] text-ink-400">Awaiting signature…</div>
                                            </div>
                                            <span class="w-3.5 h-1 rounded-full bg-ink-900/15"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="absolute bottom-6 left-6 z-10 rounded-2xl px-4 py-3 flex items-center gap-3 hero-float-card-cream hidden md:flex">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                </span>
                <div>
                    <div class="text-[11px] font-semibold text-ink-900 leading-none">127 contracts</div>
                    <div class="text-[10px] text-ink-500 mt-0.5">signed in the last hour</div>
                </div>
            </div>

            <div class="absolute top-6 right-6 z-10 rounded-2xl px-4 py-3 hero-float-card-cream hidden md:block">
                <div class="text-[10px] font-mono text-ink-400 uppercase tracking-widest mb-1.5">AI Confidence</div>
                <div class="flex items-end gap-1.5">
                    <span class="font-display text-3xl text-ink-900 leading-none">98</span>
                    <span class="text-primary-deep font-semibold text-sm mb-0.5">%</span>
                </div>
                <div class="mt-2 flex gap-0.5">
                    <div class="h-1 flex-1 rounded-full bg-primary"></div>
                    <div class="h-1 flex-1 rounded-full bg-primary"></div>
                    <div class="h-1 flex-1 rounded-full bg-primary"></div>
                    <div class="h-1 flex-1 rounded-full bg-primary"></div>
                    <div class="h-1 w-3 rounded-full bg-ink-900/15"></div>
                </div>
            </div>

            <div class="absolute bottom-6 right-6 z-10 rounded-2xl px-4 py-3 hero-float-card-cream hidden md:block">
                <div class="text-[10px] font-mono text-ink-400 uppercase tracking-widest mb-1">Time saved</div>
                <div class="flex items-baseline gap-1">
                    <span class="font-display text-2xl text-ink-900 leading-none">4.2×</span>
                    <span class="text-[11px] text-ink-500">faster than Word</span>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
$(function () {
    'use strict';
    var $el = $('#{{ $heroTyperId }}');
    if (!$el.length) return;
    var text = '4.2 Indemnification — Each party (the "Indemnifying Party") shall defend, indemnify, and hold harmless the other party from any third-party claim arising out of gross negligence or willful misconduct…';
    var i = 0;
    function tick() {
        if (i <= text.length) {
            $el.text(text.slice(0, i));
            i++;
            setTimeout(tick, 24 + Math.random() * 38);
        } else {
            setTimeout(function () { i = 0; $el.text(''); tick(); }, 3000);
        }
    }
    tick();
});
</script>
@endpush

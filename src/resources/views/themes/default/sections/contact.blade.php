@php
    $d       = $section->data ?? [];
    $eyebrow = $d['eyebrow']          ?? 'Get in touch';
    $heading = $d['heading']          ?? "Let's talk";
    $italic  = $d['heading_italic']   ?? 'contracts.';
    $sub     = $d['subheading']       ?? "Whether you're evaluating the platform, need a custom plan, or just have a question — we're here. Usually reply within two hours.";
    $respTime   = $d['response_time'] ?? '~2h';
    $respNote   = $d['response_note'] ?? 'On business days. We read every message ourselves — no bots, no ticket queues.';
    $onlineText = $d['online_text']   ?? 'Support is online · Mon–Fri, 09:00–18:00 CET';
    $entHeading = $d['enterprise_heading'] ?? 'Need a custom plan for your firm?';
    $entBody    = $d['enterprise_body']    ?? 'Volume pricing, SSO, dedicated support, custom clause training, and SLA guarantees.';
    $entCta     = $d['enterprise_cta']     ?? 'Talk to sales';
    $topics     = array_values(array_filter(array_map('trim', explode(',', $d['topics'] ?? 'Sales,Support,Enterprise,Partnership,Press,Other'))));
    $gid        = 'ct-' . $section->id;
@endphp


<section class="relative py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="ct-grid">


            <div class="glass rounded-3xl p-8 md:p-10">

                @if(session('contact_success'))
                <div class="flex flex-col items-center justify-center py-16 text-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-lime/15 border border-lime/30 grid place-items-center mx-auto">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-3xl mt-2 text-white">{{ __('Message sent.') }}</h3>
                        <p class="text-white/50 text-sm mt-2">{{ __("We'll get back to you within two hours on business days.") }}</p>
                    </div>
                    <a href="{{ url()->current() }}" class="mt-2 text-sm text-lime underline underline-offset-4">{{ __('Send another') }}</a>
                </div>
                @else

                <h2 class="font-display text-3xl mb-8 text-white">{{ __('Send us a message.') }}</h2>

                @if($errors->any())
                <div class="ct-error-box">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" novalidate>
                    @csrf
                    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">


                    @if(!empty($topics))
                    <div class="ct-topic">
                        <div class="text-[11px] font-mono uppercase tracking-widest text-white/40 mb-3">{{ __('Topic') }}</div>
                        <div class="flex flex-wrap gap-2" id="{{ $gid }}-chips">
                            @foreach($topics as $i => $topic)
                            <button type="button" data-topic="{{ $topic }}"
                                    class="ct-chip {{ $i === 0 ? 'ct-active' : '' }}">
                                {{ __($topic) }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="subject" id="{{ $gid }}-topic" value="{{ old('subject', $topics[0] ?? '') }}">
                    </div>
                    @endif


                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] font-mono uppercase tracking-widest text-white/40 mb-2">{{ __('Full name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Elena Martí" required class="ct-field">
                            @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono uppercase tracking-widest text-white/40 mb-2">{{ __('Work email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required class="ct-field">
                            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>


                    <div class="mb-6">
                        <label class="block text-[11px] font-mono uppercase tracking-widest text-white/40 mb-2">{{ __('Message') }}</label>
                        <textarea name="message" rows="5" required placeholder="{{ __('Tell us what you\'re working on…') }}" class="ct-field">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <x-turnstile />

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                        <p class="text-[12px] text-white/35 max-w-xs">
                            {{ __('By sending you agree to our') }}
                            <a href="{{ route('page.show', 'privacy-policy') }}" class="underline underline-offset-2 hover:text-white/60">{{ __('Privacy Policy') }}</a>.
                            {{ __('We never sell data.') }}
                        </p>
                        <button type="submit" class="btn-lime rounded-full px-7 py-3.5 text-[15px] flex items-center gap-2 shrink-0">
                            {{ __('Send message') }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                </form>
                @endif
            </div>


            <div class="ct-sidebar gap-4">


                <div class="glass rounded-3xl p-7 relative overflow-hidden lift">
                    <div class="absolute -right-10 -bottom-10 w-52 h-52 glow-lime opacity-50 pointer-events-none"></div>
                    <div class="relative">
                        <div class="text-[11px] font-mono uppercase tracking-widest text-lime mb-4">{{ __('Response time') }}</div>
                        <span class="font-display text-6xl leading-none text-white">{{ $respTime }}</span>
                        <p class="text-white/50 text-sm mt-3">{{ __($respNote) }}</p>
                        <div class="mt-5 flex items-center gap-2 text-[12px] text-white/40">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime opacity-60"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-lime"></span>
                            </span>
                            {{ __($onlineText) }}
                        </div>
                    </div>
                </div>


                <div class="glass rounded-3xl p-7 lift">
                    <div class="text-[11px] font-mono uppercase tracking-widest text-white/40 mb-5">{{ __('Reach us directly') }}</div>
                    <ul class="flex flex-col gap-4">
                        <li class="flex items-center gap-4">
                            <span class="ct-icon-wrap">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ __('Email') }}</div>
                                <a href="mailto:{{ config('mail.from.address') }}" class="text-sm text-white hover:text-white/70 transition">{{ config('mail.from.address') }}</a>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="glass-cream rounded-3xl p-7 lift">
                    <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-3">{{ __('Enterprise') }}</div>
                    <h3 class="font-display text-2xl leading-snug text-ink-900">{{ __($entHeading) }}</h3>
                    <p class="text-ink-500 text-sm mt-2 leading-relaxed">{{ __($entBody) }}</p>
                    <a href="{{ route('contact') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-ink-900 text-white text-sm font-medium px-5 py-2.5 hover:bg-ink-800 transition">
                        {{ __($entCta) }}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    'use strict';
    var chips = document.querySelectorAll('#{{ $gid }}-chips .ct-chip');
    var input = document.getElementById('{{ $gid }}-topic');
    if (!chips.length) return;
    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) { c.classList.remove('ct-active'); });
            chip.classList.add('ct-active');
            if (input) input.value = chip.dataset.topic;
        });
    });
})();
</script>
@endpush

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


<section class="relative py-28 {{ $sectionBg ?? 'bg-cream-50' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="ct-grid">


            <div class="glass-cream rounded-3xl p-8 md:p-10">

                @if(session('contact_success'))
                <div class="flex flex-col items-center justify-center py-16 text-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-primary/15 border border-primary/30 grid place-items-center mx-auto">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--brand-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-3xl mt-2 text-ink-950">@lang('Message sent.')</h3>
                        <p class="text-ink-500 text-sm mt-2">@lang("We'll get back to you within two hours on business days.")</p>
                    </div>
                    <a href="{{ url()->current() }}" class="mt-2 text-sm text-primary underline underline-offset-4">@lang('Send another')</a>
                </div>
                @else

                <h2 class="font-display text-3xl mb-8 text-ink-950">@lang('Send us a message.')</h2>

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
                        <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-3">@lang('Topic')</div>
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
                            <label class="block text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-2">@lang('Full name')</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Elena Martí" required class="ct-field">
                            @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-2">@lang('Work email')</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required class="ct-field">
                            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>


                    <div class="mb-6">
                        <label class="block text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-2">@lang('Message')</label>
                        <textarea name="message" rows="5" required placeholder="{{ __('Tell us what you\'re working on…') }}" class="ct-field">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <x-turnstile />

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                        <p class="text-[12px] text-ink-400 max-w-xs">
                            @lang('By sending you agree to our')
                            <a href="{{ route('page.show', 'privacy-policy') }}" class="underline underline-offset-2 hover:text-ink-600">@lang('Privacy Policy')</a>.
                            @lang('We never sell data.')
                        </p>
                        <button type="submit" class="btn-primary text-on-primary-white rounded-full px-7 py-3.5 text-[15px] flex items-center gap-2 shrink-0">
                            @lang('Send message')
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                </form>
                @endif
            </div>


            <div class="ct-sidebar gap-4">


                <div class="glass-cream rounded-3xl p-7 relative overflow-hidden lift">
                    <div class="absolute -right-10 -bottom-10 w-52 h-52 glow-primary opacity-50 pointer-events-none"></div>
                    <div class="relative">
                        <div class="text-[11px] font-mono uppercase tracking-widest text-primary mb-4">@lang('Response time')</div>
                        <span class="font-display text-6xl leading-none text-ink-950">{{ $respTime }}</span>
                        <p class="text-ink-500 text-sm mt-3">{{ __($respNote) }}</p>
                        <div class="mt-5 flex items-center gap-2 text-[12px] text-ink-400">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                            </span>
                            {{ __($onlineText) }}
                        </div>
                    </div>
                </div>


                <div class="glass-cream rounded-3xl p-7 lift">
                    <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-5">@lang('Reach us directly')</div>
                    <ul class="flex flex-col gap-4">
                        <li class="flex items-center gap-4">
                            <span class="ct-icon-wrap">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400">@lang('Email')</div>
                                <a href="mailto:{{ config('mail.from.address') }}" class="text-sm text-ink-950 hover:text-ink-700 transition">{{ config('mail.from.address') }}</a>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="glass-cream rounded-3xl p-7 lift">
                    <div class="text-[11px] font-mono uppercase tracking-widest text-ink-400 mb-3">@lang('Enterprise')</div>
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
    var $chips = $('#{{ $gid }}-chips .ct-chip');
    var $input = $('#{{ $gid }}-topic');
    if (!$chips.length) return;
    $chips.on('click', function () {
        $chips.removeClass('ct-active');
        $(this).addClass('ct-active');
        $input.val($(this).data('topic'));
    });
})();
</script>
@endpush

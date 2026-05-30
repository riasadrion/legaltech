@php
    use App\Domain\Cms\Models\Menu;
    $footerMenu = Menu::handle('footer');
    $footerColumns = $footerMenu ? $footerMenu->items()->get() : collect();

    $socials = [
        ['url' => setting('site.twitter'),  'label' => 'Twitter',  'icon' => 'twitter'],
        ['url' => setting('site.linkedin'), 'label' => 'LinkedIn', 'icon' => 'linkedin'],
        ['url' => setting('site.github'),   'label' => 'GitHub',   'icon' => 'github'],
        ['url' => setting('site.youtube'),  'label' => 'YouTube',  'icon' => 'youtube'],
    ];
    $hasSocials = collect($socials)->contains(fn($s) => !empty($s['url']));
@endphp
<footer class="relative border-t border-white/8 bg-ink-950">
    <div class="mx-auto max-w-7xl px-6 py-20">

        <div class="footer-main-grid">


            @php
                $footerSiteName = setting('site.name', config('app.name'));
                $footerLogo     = setting('site.logo');
                $footerDarkLogo = setting('site.dark_logo');
            @endphp
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if($footerLogo || $footerDarkLogo)
                        @if($footerLogo)
                            <img src="{{ upload_url($footerLogo) }}" alt="{{ $footerSiteName }}" class="nav-brand-logo nav-brand-logo-light h-9">
                        @endif
                        @if($footerDarkLogo)
                            <img src="{{ upload_url($footerDarkLogo) }}" alt="{{ $footerSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-9">
                        @elseif($footerLogo)
                            <img src="{{ upload_url($footerLogo) }}" alt="{{ $footerSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-9">
                        @endif
                    @else
                        <span class="grid place-items-center w-9 h-9 rounded-full bg-lime text-ink-900">
                            <x-heroicon-s-pencil class="h-5 w-5" />
                        </span>
                        <span class="font-display text-3xl text-white">{{ $footerSiteName }}</span>
                    @endif
                </a>
                <p class="text-white/55 text-sm mt-4 max-w-sm">
                    {{ setting('site.tagline', __('Built for modern teams. Fast, secure, and ready out of the box.')) }}
                </p>


                <form id="newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}"
                      class="relative mt-8 flex items-center gap-2 glass rounded-full p-1.5 pl-4 max-w-sm">
                    @csrf
                    <x-heroicon-o-envelope class="h-4 w-4 text-white/40 shrink-0" />
                    <input type="email" name="email" required
                           placeholder="{{ __('you@company.com') }}"
                           class="flex-1 min-w-0 bg-transparent outline-none text-sm placeholder:text-white/35 text-white py-2">

                    <input type="text" name="website" tabindex="-1" autocomplete="off"
                           class="absolute opacity-0 pointer-events-none -z-10" aria-hidden="true">
                    <button type="submit" class="btn-lime rounded-full text-sm px-4 py-2 shrink-0">
                        <span data-label>{{ __('Subscribe') }}</span>
                    </button>
                </form>
                <p id="newsletter-msg" class="text-[11px] mt-2 text-white/40">
                    {{ __('One short email a month. No tracking pixels.') }}
                </p>
            </div>


            <div class="footer-nav-grid">
            @foreach($footerColumns as $column)
                @if($column->children->isNotEmpty())
                <div>
                    <div class="text-[11px] font-mono uppercase tracking-widest text-white/40 mb-4">
                        {{ $column->getLocalLabel() }}
                    </div>
                    <ul class="space-y-2 text-sm text-white/70">
                        @foreach($column->children as $link)
                        <li>
                            <a href="{{ $link->resolvedUrl() }}"
                               @if($link->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                               class="hover:text-white transition">
                                {{ $link->getLocalLabel() }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div>
                    <a href="{{ $column->resolvedUrl() }}"
                       @if($column->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                       class="text-sm text-white/70 hover:text-white transition">
                        {{ $column->getLocalLabel() }}
                    </a>
                </div>
                @endif
            @endforeach
            </div>

        </div>

        <div class="mt-14 pt-8 border-t border-white/8 flex flex-wrap items-center justify-between gap-4">
            @if($hasSocials)
            <div class="flex items-center gap-3 text-white/55">
                @foreach($socials as $s)
                    @if(!empty($s['url']))
                        <a href="{{ $s['url'] }}" aria-label="{{ $s['label'] }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 grid place-items-center rounded-full glass hover:text-white">
                            @switch($s['icon'])
                                @case('twitter')
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>
                                    </svg>
                                    @break
                                @case('linkedin')
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    @break
                                @case('github')
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.6.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.5 11.5 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    @break
                                @case('youtube')
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    @break
                            @endswitch
                        </a>
                    @endif
                @endforeach
            </div>
            @endif

            <div class="text-xs text-white/45 {{ $hasSocials ? '' : 'w-full text-center' }}">
                &copy; {{ now()->year }} {{ setting('site.name', config('app.name')) }}.
                {{ __('All rights reserved.') }}
            </div>

            @if($hasSocials)<div></div>@endif
        </div>
    </div>
</footer>

@push('scripts')
<script>
$(function () {
    'use strict';

    var $form = $('#newsletter-form');
    if (!$form.length) return;

    var $msg   = $('#newsletter-msg');
    var $btn   = $form.find('button[type="submit"]');
    var $label = $btn.find('[data-label]');
    var $input = $form.find('input[name="email"]');

    var idleMsg   = $msg.text();
    var idleClass = 'text-white/40';
    var okClass   = 'text-lime';
    var errClass  = 'text-red-400';
    var classes   = idleClass + ' ' + okClass + ' ' + errClass;

    var idleLabel = $label.text();

    $form.on('submit', function (e) {
        e.preventDefault();

        $btn.prop('disabled', true).addClass('opacity-70');
        $label.text('{{ __('Sending…') }}');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        }).done(function (res) {
            var color = res.status === 'info' ? idleClass : okClass;
            $msg.removeClass(classes).addClass(color).text(res.message || '{{ __('Subscribed.') }}');
            $label.text('{{ __('Subscribed ✓') }}');
            $input.val('');
        }).fail(function (xhr) {
            var message = '{{ __('Something went wrong. Please try again.') }}';
            if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.email) {
                message = xhr.responseJSON.errors.email[0];
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            $msg.removeClass(classes).addClass(errClass).text(message);
            $label.text(idleLabel);
            $btn.prop('disabled', false).removeClass('opacity-70');
        });
    });
});
</script>
@endpush

@php
    use App\Models\Menu;
    $headerMenu = Menu::handle('header');
    $navItems = $headerMenu ? $headerMenu->items()->get() : collect();
@endphp
<header class="fixed top-0 inset-x-0 z-50">
    <div class="mx-auto max-w-7xl px-6 pt-5">
        <nav class="relative glass-cream rounded-full px-3 py-2.5 flex items-center justify-between">


            @php
                $navSiteName = setting('site.name', config('app.name'));
                $navLogo     = setting('site.logo');
                $navDarkLogo = setting('site.dark_logo');
            @endphp
            <a href="{{ route('home') }}" class="flex items-center gap-2 pl-1">
                @if($navLogo || $navDarkLogo)
                    @if($navDarkLogo)
                        <img src="{{ upload_url($navDarkLogo) }}" alt="{{ $navSiteName }}" class="nav-brand-logo">
                    @elseif($navLogo)
                        <img src="{{ upload_url($navLogo) }}" alt="{{ $navSiteName }}" class="nav-brand-logo">
                    @endif
                @else
                    <span class="relative grid place-items-center w-8 h-8 rounded-full bg-primary text-ink-900">
                        <x-heroicon-s-pencil class="h-4 w-4" />
                    </span>
                    <span class="font-display text-2xl tracking-tight text-ink-950">{{ $navSiteName }}</span>
                @endif
            </a>


            @if($navItems->isNotEmpty())
            <ul class="hidden lg:flex items-center gap-7 text-sm text-ink-500 absolute left-1/2 -translate-x-1/2">
                @foreach($navItems as $item)
                    @if($item->children->isNotEmpty())
                    <li class="relative group">
                        <button type="button" class="flex items-center gap-1 hover:text-ink-950 transition">
                            {{ $item->getLocalLabel() }}
                            <x-heroicon-o-chevron-down class="h-3 w-3 transition group-hover:rotate-180" />
                        </button>
                        <ul class="absolute top-full left-1/2 -translate-x-1/2 mt-3 min-w-40 rounded-2xl glass-cream py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-10">
                            @foreach($item->children as $child)
                            <li>
                                <a href="{{ $child->resolvedUrl() }}"
                                   @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                   class="block px-4 py-2 text-sm text-ink-500 hover:text-ink-950 hover:bg-ink-900/5 transition">
                                    {{ $child->getLocalLabel() }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @else
                    <li>
                        <a href="{{ $item->resolvedUrl() }}"
                           @if($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                           class="hover:text-ink-950 transition">
                            {{ $item->getLocalLabel() }}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
            @endif


            @php
                $navLanguages = \App\Models\Language::where('is_active', true)->orderBy('name')->get();
                $currentLocale = app()->getLocale();
                $currentLang = $navLanguages->firstWhere('code', $currentLocale) ?? $navLanguages->first();
            @endphp

            <div class="flex items-center gap-2">

                @if($navLanguages->count() > 1)
                <div class="relative group">
                    <button type="button" class="flex items-center gap-1.5 text-sm text-ink-500 hover:text-ink-950 transition px-2 py-1.5">
                        <x-heroicon-o-language class="h-4 w-4" />
                        <span class="hidden sm:inline uppercase text-[11px] font-mono tracking-wider">{{ $currentLang?->code ?? $currentLocale }}</span>
                        <x-heroicon-o-chevron-down class="h-3 w-3 transition group-hover:rotate-180" />
                    </button>
                    <ul class="absolute top-full right-0 mt-2 min-w-36 rounded-2xl glass-cream py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-20">
                        @foreach($navLanguages as $lang)
                        <li>
                            <button type="button"
                                    data-locale-switch="{{ $lang->code }}"
                                    data-locale-url="{{ route('locale.set', $lang->code) }}"
                                    class="w-full text-left px-4 py-2 text-sm transition
                                           {{ $lang->code === $currentLocale ? 'text-primary-deep font-medium' : 'text-ink-500 hover:text-ink-950 hover:bg-ink-900/5' }}">
                                {{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @guest('user')
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline text-sm px-4 py-2 text-ink-700 hover:text-ink-950">
                        @lang('Sign in')
                    </a>
                    <a href="{{ route('register') }}"
                       class="btn-primary no-shadow text-on-primary-white rounded-full px-5 py-2.5 text-sm hidden sm:flex items-center gap-2">
                        @lang('Get started')
                        <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}"
                       class="hidden sm:inline text-sm px-4 py-2 text-ink-700 hover:text-ink-950">
                        @lang('Dashboard')
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                        @csrf
                        <button type="submit" class="btn-primary no-shadow text-on-primary-white rounded-full px-5 py-2.5 text-sm flex items-center gap-2">
                            @lang('Sign out')
                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                        </button>
                    </form>
                @endguest


                <button type="button" id="nav-mobile-btn"
                        class="lg:hidden ml-2 w-9 h-9 grid place-items-center rounded-full glass-cream text-ink-500 hover:text-ink-950"
                        aria-label="{{ __('Toggle navigation menu') }}"
                        aria-expanded="false"
                        aria-controls="nav-mobile-drawer">
                    <x-heroicon-o-bars-3 class="h-5 w-5" />
                </button>
            </div>

        </nav>


        <div id="nav-mobile-drawer"
             class="lg:hidden hidden mt-2 glass-cream rounded-2xl p-4">
            @if($navItems->isNotEmpty())
            <ul class="space-y-1 text-sm">
                @foreach($navItems as $item)
                    @if($item->children->isNotEmpty())
                        <li>
                            <div class="px-3 py-2 font-medium text-ink-400 text-[11px] uppercase tracking-widest">
                                {{ $item->getLocalLabel() }}
                            </div>
                            <ul class="ml-3 space-y-1">
                                @foreach($item->children as $child)
                                <li>
                                    <a href="{{ $child->resolvedUrl() }}"
                                       @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                       class="block px-3 py-2 rounded-xl text-ink-500 hover:text-ink-950 hover:bg-ink-900/5">
                                        {{ $child->getLocalLabel() }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                    <li>
                        <a href="{{ $item->resolvedUrl() }}"
                           @if($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                           class="block px-3 py-2 rounded-xl text-ink-500 hover:text-ink-950 hover:bg-ink-900/5">
                            {{ $item->getLocalLabel() }}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
            @endif

            @if($navLanguages->count() > 1)
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach($navLanguages as $lang)
                <button type="button"
                        data-locale-switch="{{ $lang->code }}"
                        data-locale-url="{{ route('locale.set', $lang->code) }}"
                        class="px-3 py-1.5 rounded-full text-[11px] font-mono uppercase tracking-wider transition
                               {{ $lang->code === $currentLocale ? 'bg-primary text-ink-900 font-semibold' : 'glass-cream text-ink-500 hover:text-ink-950' }}">
                    {{ $lang->code }}
                </button>
                @endforeach
            </div>
            @endif

            <div class="mt-4 pt-4 border-t border-ink-900/10 flex flex-col gap-2">
                @guest('user')
                    <a href="{{ route('login') }}" class="block text-center text-sm px-4 py-2.5 rounded-xl glass-cream text-ink-700 hover:text-ink-950">
                        @lang('Sign in')
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary no-shadow text-on-primary-white rounded-xl text-center px-4 py-2.5 text-sm">
                        @lang('Get started')
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="block text-center text-sm px-4 py-2.5 rounded-xl glass-cream text-ink-700 hover:text-ink-950">
                        @lang('Dashboard')
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-primary no-shadow text-on-primary-white w-full rounded-xl text-center px-4 py-2.5 text-sm">
                            @lang('Sign out')
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
$(function () {
    'use strict';
    $('#nav-mobile-btn').on('click', function () {
        var $btn = $(this);
        var $drawer = $('#nav-mobile-drawer').toggleClass('hidden');
        $btn.attr('aria-expanded', !$drawer.hasClass('hidden'));
    });
});
</script>
@endpush

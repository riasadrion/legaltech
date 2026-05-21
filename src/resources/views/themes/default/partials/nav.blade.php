@php
    use App\Domain\Cms\Models\Menu;
    $headerMenu = Menu::handle('header');
    $navItems = $headerMenu ? $headerMenu->items()->get() : collect();
@endphp
<header class="fixed top-0 inset-x-0 z-50">
    <div class="mx-auto max-w-7xl px-6 pt-5">
        <nav class="glass-nav rounded-full px-3 py-2.5 flex items-center justify-between">

            {{-- Logo / Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 pl-3">
                @if($logo = setting('site.logo'))
                    <img src="{{ $logo }}" alt="{{ setting('site.name', config('app.name')) }}" class="h-8">
                @else
                    <span class="relative grid place-items-center w-8 h-8 rounded-full bg-lime text-ink-900">
                        <x-heroicon-s-pencil class="h-4 w-4" />
                    </span>
                    <span class="font-display text-2xl tracking-tight text-white">{{ setting('site.name', config('app.name')) }}</span>
                @endif
            </a>

            {{-- Center nav links --}}
            @if($navItems->isNotEmpty())
            <ul class="hidden lg:flex items-center gap-7 text-sm text-white/70">
                @foreach($navItems as $item)
                    @if($item->children->isNotEmpty())
                    <li class="relative group">
                        <button type="button" class="flex items-center gap-1 hover:text-white transition">
                            {{ $item->getLocalLabel() }}
                            <x-heroicon-o-chevron-down class="h-3 w-3 transition group-hover:rotate-180" />
                        </button>
                        <ul class="absolute top-full left-1/2 -translate-x-1/2 mt-3 min-w-40 rounded-2xl glass py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-10">
                            @foreach($item->children as $child)
                            <li>
                                <a href="{{ $child->resolvedUrl() }}"
                                   @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                   class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-white/5 transition">
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
                           class="hover:text-white transition">
                            {{ $item->getLocalLabel() }}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
            @endif

            {{-- Right side: auth state --}}
            <div class="flex items-center gap-2">
                @guest('user')
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline text-sm px-4 py-2 text-white/80 hover:text-white">
                        {{ __('Sign in') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="btn-lime rounded-full px-5 py-2.5 text-sm flex items-center gap-2">
                        {{ __('Start free trial') }}
                        <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}"
                       class="hidden sm:inline text-sm px-4 py-2 text-white/80 hover:text-white">
                        {{ __('Dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-lime rounded-full px-5 py-2.5 text-sm flex items-center gap-2">
                            {{ __('Sign out') }}
                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                        </button>
                    </form>
                @endguest

                {{-- Mobile hamburger --}}
                <button type="button" id="nav-mobile-btn"
                        class="lg:hidden ml-2 w-9 h-9 grid place-items-center rounded-full glass text-white/70 hover:text-white">
                    <x-heroicon-o-bars-3 class="h-5 w-5" />
                </button>
            </div>

        </nav>

        {{-- Mobile drawer --}}
        <div id="nav-mobile-drawer"
             class="lg:hidden hidden mt-2 glass-nav rounded-2xl p-4">
            @if($navItems->isNotEmpty())
            <ul class="space-y-1 text-sm">
                @foreach($navItems as $item)
                    @if($item->children->isNotEmpty())
                        <li>
                            <div class="px-3 py-2 font-medium text-white/50 text-[11px] uppercase tracking-widest">
                                {{ $item->getLocalLabel() }}
                            </div>
                            <ul class="ml-3 space-y-1">
                                @foreach($item->children as $child)
                                <li>
                                    <a href="{{ $child->resolvedUrl() }}"
                                       @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                       class="block px-3 py-2 rounded-xl text-white/70 hover:text-white hover:bg-white/5">
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
                           class="block px-3 py-2 rounded-xl text-white/70 hover:text-white hover:bg-white/5">
                            {{ $item->getLocalLabel() }}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
            @endif

            <div class="mt-4 pt-4 border-t border-white/10 flex flex-col gap-2">
                @guest('user')
                    <a href="{{ route('login') }}" class="block text-center text-sm px-4 py-2.5 rounded-xl glass text-white/80 hover:text-white">
                        {{ __('Sign in') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn-lime rounded-xl text-center px-4 py-2.5 text-sm">
                        {{ __('Start free trial') }}
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="block text-center text-sm px-4 py-2.5 rounded-xl glass text-white/80 hover:text-white">
                        {{ __('Dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-lime w-full rounded-xl text-center px-4 py-2.5 text-sm">
                            {{ __('Sign out') }}
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
        $('#nav-mobile-drawer').toggleClass('hidden');
    });
});
</script>
@endpush

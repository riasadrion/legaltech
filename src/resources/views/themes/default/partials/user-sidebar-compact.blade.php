{{-- Brand --}}
@php
    $uSiteName   = setting('site.name', config('app.name'));
    $uLogo       = setting('site.logo');
    $uDarkLogo   = setting('site.dark_logo');
@endphp
<div class="px-5 py-5 flex items-center gap-2 border-b border-white/8">
    <a href="{{ route('home') }}" class="flex items-center gap-2">
        @if($uLogo || $uDarkLogo)
            @if($uLogo)
                <img src="{{ $uLogo }}" alt="{{ $uSiteName }}" class="nav-brand-logo nav-brand-logo-light h-8">
            @endif
            @if($uDarkLogo)
                <img src="{{ $uDarkLogo }}" alt="{{ $uSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
            @elseif($uLogo)
                <img src="{{ $uLogo }}" alt="{{ $uSiteName }}" class="nav-brand-logo nav-brand-logo-dark h-8">
            @endif
        @else
            <span class="grid place-items-center w-8 h-8 rounded-full bg-lime text-ink-900">
                <x-heroicon-s-pencil class="h-4 w-4" />
            </span>
            <span class="font-display text-2xl text-white">{{ $uSiteName }}</span>
        @endif
    </a>
</div>

{{-- User pill --}}
@auth('user')
<div class="px-4 pt-4">
    <a href="{{ route('user.profile') }}"
       class="w-full flex items-center gap-2 rounded-xl px-3 py-2.5 bg-white/4 border border-white/8 hover:bg-white/6 transition">
        <span class="w-7 h-7 rounded-md bg-gradient-to-br from-lime to-lime-deep grid place-items-center text-ink-900 font-bold text-[11px]">
            {{ mb_strtoupper(mb_substr(auth('user')->user()->name ?? auth('user')->user()->email, 0, 1)) }}
        </span>
        <div class="text-left flex-1 min-w-0">
            <div class="text-[13px] truncate text-white">{{ auth('user')->user()->name ?? __('Member') }}</div>
            <div class="text-[10px] text-white/45 font-mono uppercase tracking-widest truncate">{{ auth('user')->user()->email }}</div>
        </div>
    </a>
</div>
@endauth

{{-- Nav --}}
@php
    $isActive = fn (string $pattern) => request()->routeIs($pattern);
    $linkBase = 'flex items-center gap-3 rounded-lg px-3 py-2 text-[13.5px] transition';
    $linkInactive = 'text-white/70 hover:bg-white/5 hover:text-white';
    $linkActive = 'bg-white/8 text-white';
    $sectionLabel = 'text-[10px] font-mono uppercase tracking-widest text-white/35 px-3 mb-1 mt-5';
@endphp

<nav class="mt-5 px-4 space-y-0.5 flex-1 overflow-y-auto no-sb">

    {{-- Workspace --}}
    <div class="text-[10px] font-mono uppercase tracking-widest text-white/35 px-3 mb-1">{{ __('Workspace') }}</div>

    <a href="{{ route('user.dashboard') }}"
       class="{{ $linkBase }} {{ $isActive('user.dashboard') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-home class="h-4 w-4" />
        {{ __('Dashboard') }}
    </a>

    <a href="{{ route('user.wallet.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.wallet.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-banknotes class="h-4 w-4" />
        {{ __('Wallet') }}
    </a>

    <a href="{{ route('user.subscription.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.subscription.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-square-3-stack-3d class="h-4 w-4" />
        {{ __('Subscription') }}
    </a>

    {{-- Finances --}}
    <div class="{{ $sectionLabel }}">{{ __('Finances') }}</div>

    <a href="{{ route('user.payment.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.payment.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-credit-card class="h-4 w-4" />
        {{ __('Payments') }}
    </a>

    <a href="{{ route('user.payout.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.payout.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
        {{ __('Payouts') }}
    </a>

    {{-- Account --}}
    <div class="{{ $sectionLabel }}">{{ __('Account') }}</div>

    <a href="{{ route('user.kyc.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.kyc.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-identification class="h-4 w-4" />
        {{ __('Verification') }}
    </a>

    <a href="{{ route('user.2fa.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.2fa.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-shield-check class="h-4 w-4" />
        {{ __('Security') }}
    </a>

    <a href="{{ route('user.notifications.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.notifications.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-bell class="h-4 w-4" />
        {{ __('Notifications') }}
    </a>

    <a href="{{ route('user.support.index') }}"
       class="{{ $linkBase }} {{ $isActive('user.support.*') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-lifebuoy class="h-4 w-4" />
        {{ __('Support') }}
    </a>

    <a href="{{ route('user.profile') }}"
       class="{{ $linkBase }} {{ $isActive('user.profile') ? $linkActive : $linkInactive }}">
        <x-heroicon-o-user class="h-4 w-4" />
        {{ __('Profile') }}
    </a>
</nav>

{{-- Footer card --}}
@auth('user')
@php
    $balanceCache = auth('user')->user()->balance_cache ?? 0;
@endphp
<div class="mt-auto p-4 shrink-0">
    <div class="rounded-2xl border border-white/8 p-4 bg-white/2">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Wallet') }}</span>
            <a href="{{ route('user.payment.create') }}" class="text-[10px] text-lime hover:underline">{{ __('Top up') }}</a>
        </div>
        <div class="mt-3 text-[15px] text-white font-medium">
            {{ setting('site.currency_symbol', '$') }}{{ number_format($balanceCache / 100, 2) }}
        </div>
        <a href="{{ route('user.wallet.index') }}"
           class="mt-3 text-[11px] text-lime hover:underline inline-flex items-center gap-1">
            {{ __('View history') }}
            <x-heroicon-o-arrow-right class="h-3 w-3" />
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-2 px-2 py-2 text-[12px] text-white/55 hover:text-white">
            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
            {{ __('Sign out') }}
        </button>
    </form>
</div>
@endauth

@php
    $uSiteName = setting('site.name', config('app.name'));
    $uLogo     = setting('site.logo');
    $uDarkLogo = setting('site.dark_logo');
    $isActive  = fn (string $pattern) => request()->routeIs($pattern);
@endphp

<!-- brand -->
<div class="px-5 h-[65px] flex items-center gap-2 border-b border-white/8 shrink-0">
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
                <x-heroicon-s-pencil class="h-[18px] w-[18px]" />
            </span>
            <span class="font-display text-2xl text-white">{{ $uSiteName }}</span>
        @endif
    </a>
</div>


<!-- nav -->
<nav class="mt-5 px-4 space-y-0.5 flex-1 overflow-y-auto no-sb">

    <div class="text-[10px] font-mono uppercase tracking-widest text-white/35 px-3 mb-1">{{ __('Workspace') }}</div>

    <a href="{{ route('user.dashboard') }}" class="nav-item {{ $isActive('user.dashboard') ? 'active' : '' }}">
        <x-heroicon-o-home class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Dashboard') }}
    </a>


    <a href="{{ route('user.contracts.index') }}" class="nav-item {{ $isActive('user.contracts.*') ? 'active' : '' }}">
        <x-heroicon-o-document-text class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Contracts') }}
    </a>

    <a href="{{ route('user.subscription.index') }}" class="nav-item {{ $isActive('user.subscription.*') ? 'active' : '' }}">
        <x-heroicon-o-square-3-stack-3d class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Subscription') }}
    </a>

    <a href="{{ route('user.payment.index') }}" class="nav-item {{ $isActive('user.payment.*') ? 'active' : '' }}">
        <x-heroicon-o-credit-card class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Payments') }}
    </a>

    <div class="text-[10px] font-mono uppercase tracking-widest text-white/35 px-3 mt-5 mb-1">{{ __('Account') }}</div>

    <a href="{{ route('user.kyc.index') }}" class="nav-item {{ $isActive('user.kyc.*') ? 'active' : '' }}">
        <x-heroicon-o-identification class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Verification') }}
    </a>

    <a href="{{ route('user.2fa.index') }}" class="nav-item {{ $isActive('user.2fa.*') ? 'active' : '' }}">
        <x-heroicon-o-shield-check class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Security') }}
    </a>

    @php
        $sidebarUnread = 0;
        try { $sidebarUnread = auth('user')->check() ? auth('user')->user()->notifications()->whereNull('read_at')->count() : 0; } catch(\Throwable) {}
    @endphp
    <a href="{{ route('user.notifications.index') }}" class="nav-item {{ $isActive('user.notifications.*') ? 'active' : '' }}">
        <x-heroicon-o-bell class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Notifications') }}
        @if($sidebarUnread > 0)
            <span class="ml-auto pill pill-sign">{{ $sidebarUnread > 9 ? '9+' : $sidebarUnread }}</span>
        @endif
    </a>

    <a href="{{ route('user.support.index') }}" class="nav-item {{ $isActive('user.support.*') ? 'active' : '' }}">
        <x-heroicon-o-lifebuoy class="h-[1rem] w-[1rem] shrink-0" />
        {{ __('Support') }}
    </a>

</nav>

<!-- usage / plan card -->
@auth('user')
@php
    $walletService  = app(\App\Domain\Wallet\Services\WalletService::class);
    $sidebarBalance = $walletService->getBalance(auth('user')->user());
    $sub            = auth('user')->user()->subscription;
    $subActive      = $sub && $sub->isActive();
@endphp
<div class="mt-auto p-4 shrink-0">
    <div class="rounded-2xl border border-white/8 p-4 bg-white/2">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Plan usage') }}</span>
            <span class="text-[10px] text-lime">{{ $subActive ? $sub->plan->name : __('Free') }}</span>
        </div>
        <div class="mt-3 text-[13px] text-white">{{ $sidebarBalance->format() }} {{ __('balance') }}</div>
        @if($subActive && $sub->credits_total > 0)
        <div class="mt-2 h-1.5 rounded-full bg-white/8 overflow-hidden">
            @php $pct = min(100, round(($sub->credits_remaining / $sub->credits_total) * 100)); @endphp
            <div class="h-full bg-lime rounded-full" style="width:{{ $pct }}%"></div>
        </div>
        <div class="mt-1 text-[11px] text-white/40">{{ number_format($sub->credits_remaining) }} / {{ number_format($sub->credits_total) }} {{ __('credits') }}</div>
        @endif
        <a href="{{ route('user.subscription.plans') }}" class="mt-3 text-[11px] text-lime hover:underline inline-flex items-center gap-1">
            {{ __('Manage plan') }} <x-heroicon-o-arrow-right class="h-3 w-3" />
        </a>
    </div>
</div>
@endauth

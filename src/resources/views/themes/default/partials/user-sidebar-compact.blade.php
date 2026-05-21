{{-- User Sidebar Navigation (Compact) --}}
<nav class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm h-fit sticky top-20">
    {{-- Navigation Menu --}}
    <div class="space-y-1">
        {{-- Dashboard --}}
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
            <x-heroicon-o-home class="h-5 w-5 flex-shrink-0" />
            <span>Dashboard</span>
        </a>

        {{-- Account Section --}}
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Account</p>
            <div class="mt-2 space-y-1">
                <a href="{{ route('user.subscription.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.subscription.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-ticket class="h-5 w-5 flex-shrink-0" />
                    <span>Subscription</span>
                </a>
                <a href="{{ route('user.wallet.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.wallet.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-wallet class="h-5 w-5 flex-shrink-0" />
                    <span>Wallet</span>
                </a>
                <a href="{{ route('user.profile') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.profile') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-user class="h-5 w-5 flex-shrink-0" />
                    <span>Profile</span>
                </a>
            </div>
        </div>

        {{-- Finances Section --}}
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Finances</p>
            <div class="mt-2 space-y-1">
                <a href="{{ route('user.payment.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.payment.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-arrow-down-tray class="h-5 w-5 flex-shrink-0" />
                    <span>Payments</span>
                </a>
                <a href="{{ route('user.payout.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.payout.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-arrow-up-tray class="h-5 w-5 flex-shrink-0" />
                    <span>Payouts</span>
                </a>
            </div>
        </div>

        {{-- Compliance Section --}}
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Compliance</p>
            <div class="mt-2 space-y-1">
                <a href="{{ route('user.kyc.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.kyc.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-identification class="h-5 w-5 flex-shrink-0" />
                    <span>KYC</span>
                </a>
                <a href="{{ route('user.2fa.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.2fa.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-shield-check class="h-5 w-5 flex-shrink-0" />
                    <span>Security</span>
                </a>
            </div>
        </div>

        {{-- Support Section --}}
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Support</p>
            <div class="mt-2 space-y-1">
                <a href="{{ route('user.support.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.support.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 flex-shrink-0" />
                    <span>Support</span>
                </a>
                <a href="{{ route('user.notifications.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('user.notifications.*') ? 'bg-brand-50 text-brand-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-heroicon-o-bell class="h-5 w-5 flex-shrink-0" />
                    <span>Notifications</span>
                </a>
            </div>
        </div>

        {{-- Logout --}}
        <div class="border-t border-gray-200 pt-2 mt-2">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50">
                    <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5 flex-shrink-0" />
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>

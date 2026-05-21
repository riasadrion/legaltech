<nav class="border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
        <a href="{{ route('home') }}" class="text-lg font-bold text-brand-600">
            {{ config('app.name') }}
        </a>
        <div class="flex items-center gap-4 text-sm">
            @guest
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700">{{ __('Sign up') }}</a>
            @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.subscription.plans') }}" class="text-gray-600 hover:text-gray-900">Plans</a>
                    <a href="{{ route('user.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>

                    {{-- User menu dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <span>{{ auth('user')->user()->name ?: auth('user')->user()->email }}</span>
                            <x-heroicon-o-chevron-down class="h-4 w-4" />
                        </button>

                        {{-- Dropdown menu --}}
                        <div class="absolute right-0 mt-0 w-48 rounded-lg border border-gray-200 bg-white shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="{{ route('user.subscription.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Subscription') }}</a>
                                <a href="{{ route('user.wallet.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Wallet') }}</a>
                                <a href="{{ route('user.payment.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Payments') }}</a>
                                <a href="{{ route('user.payout.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Payouts') }}</a>
                                <div class="border-t border-gray-100"></div>
                                <a href="{{ route('user.support.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Support') }}</a>
                                <a href="{{ route('user.notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Notifications') }}</a>
                                <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Profile') }}</a>
                                <a href="{{ route('user.2fa.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Security') }}</a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</nav>

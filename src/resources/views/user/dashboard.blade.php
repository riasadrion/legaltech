@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">

    {{-- Welcome Section --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Welcome back, :name', ['name' => auth('user')->user()->name ?? 'User']) }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Subscription Status --}}
        @php $subscription = auth('user')->user()->subscription; @endphp
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Subscription') }}</p>
                    @if($subscription && $subscription->isActive())
                        <p class="mt-2 text-xl font-bold text-brand-600">{{ $subscription->plan->name }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Active') }}</p>
                    @else
                        <p class="mt-2 text-xl font-bold text-gray-900">{{ __('No Plan') }}</p>
                        <a href="{{ route('user.subscription.plans') }}" class="mt-1 text-xs font-semibold text-brand-600 hover:text-brand-700">{{ __('Browse plans') }} →</a>
                    @endif
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 flex-shrink-0">
                    <x-heroicon-o-ticket class="h-5 w-5" />
                </div>
            </div>
        </div>

        {{-- Credits Remaining --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Credits') }}</p>
                    @if($subscription && $subscription->isActive())
                        <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($subscription->credits_remaining) }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Remaining') }}</p>
                    @else
                        <p class="mt-2 text-xl font-bold text-gray-400">—</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('No active plan') }}</p>
                    @endif
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 flex-shrink-0">
                    <x-heroicon-o-sparkles class="h-5 w-5" />
                </div>
            </div>
        </div>
    </div>

    {{-- Subscription Details (if active) --}}
    @if($subscription && $subscription->isActive())
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">{{ __('Subscription Details') }}</h2>
            <a href="{{ route('user.subscription.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">{{ __('Manage') }} →</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
            <div>
                <p class="text-xs text-gray-500">{{ __('Plan') }}</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->plan->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Status') }}</p>
                <p class="mt-1">
                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Ends') }}</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->current_period_end->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div>
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Quick Actions') }}</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <a href="{{ route('user.payment.index') }}" class="group rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-brand-400 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100">
                        <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ __('Add Funds') }}</p>
                        <p class="text-xs text-gray-400">{{ __('Payment funds') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('user.subscription.plans') }}" class="group rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-brand-400 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100">
                        <x-heroicon-o-ticket class="h-4 w-4" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ __('Browse Plans') }}</p>
                        <p class="text-xs text-gray-400">{{ __('Upgrade plan') }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Transactions --}}
    @php
        try {
            $payments = auth('user')->user()->payments()->latest()->limit(5)->get();
        } catch (\Exception $e) {
            $payments = collect([]);
        }
    @endphp
    @if($payments->isNotEmpty())
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-3">
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Recent Activity') }}</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($payments as $payment)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 flex-shrink-0">
                        <x-heroicon-o-arrow-down-tray class="h-4 w-4 text-gray-600" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $payment->gateway->name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <p class="text-xs font-semibold text-gray-900">{{ $payment->formattedAmount() }}</p>
                    @php $sc = match($payment->status) { 'completed' => 'bg-green-100 text-green-700', 'awaiting_approval' => 'bg-amber-100 text-amber-700', 'failed' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-500' }; @endphp
                    <span class="mt-0.5 inline-flex rounded-full px-1.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ __(str_replace('_', ' ', $payment->status)) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

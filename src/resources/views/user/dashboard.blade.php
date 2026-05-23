@extends('theme::layouts.user')

@section('content')
@php
    $user         = auth('user')->user();
    $subscription = $user->subscription;
    $isActive     = $subscription && $subscription->isActive();
    $balance      = $user->balance_cache ?? 0;
    $currency     = setting('site.currency_symbol', '$');
@endphp

<div class="space-y-6">

    {{-- Page heading --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="ud-page-title">{{ __('Welcome back') }}{{ $user->name ? ', ' . $user->name : '' }}</h1>
            <p class="ud-page-sub">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <a href="{{ route('user.payment.create') }}" class="ud-btn-primary hidden sm:inline-flex">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('Add Funds') }}
        </a>
    </div>

    {{-- Nudges --}}
    @if(count($nudges))
    <div class="space-y-2" id="nudge-list">
        @foreach($nudges as $nudge)
        <div class="ud-nudge" data-nudge="{{ $nudge['key'] }}">
            <x-heroicon-o-information-circle class="ud-nudge-icon h-5 w-5" />
            <div class="flex-1 min-w-0">
                <div class="ud-nudge-title">{{ $nudge['title'] }}</div>
                <div class="ud-nudge-body">{{ $nudge['body'] }}</div>
            </div>
            <a href="{{ $nudge['action'] }}" class="ud-nudge-action">{{ $nudge['label'] }} →</a>
            <button type="button" class="ud-nudge-dismiss" data-dismiss-nudge="{{ $nudge['key'] }}" aria-label="{{ __('Dismiss') }}">
                <x-heroicon-o-x-mark class="h-4 w-4" />
            </button>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Stats row --}}
    <div class="ud-stat-grid">

        {{-- Wallet balance --}}
        <div class="ud-stat">
            <div class="ud-stat-icon bg-lime/10">
                <x-heroicon-o-banknotes class="h-4 w-4 text-lime" />
            </div>
            <div class="ud-stat-label">{{ __('Wallet') }}</div>
            <div class="ud-stat-value">{{ $currency }}{{ number_format($balance / 100, 2) }}</div>
            <div class="ud-stat-sub">
                <a href="{{ route('user.wallet.index') }}" class="ud-card-link">{{ __('View history') }} →</a>
            </div>
        </div>

        {{-- Subscription --}}
        <div class="ud-stat">
            <div class="ud-stat-icon bg-violet-500/10">
                <x-heroicon-o-square-3-stack-3d class="h-4 w-4 text-violet-400" />
            </div>
            <div class="ud-stat-label">{{ __('Plan') }}</div>
            @if($isActive)
                <div class="ud-stat-value text-[1.1rem]">{{ $subscription->plan->name }}</div>
                <div class="ud-stat-sub mt-1"><span class="ud-badge ud-badge-green">{{ __('Active') }}</span></div>
            @else
                <div class="ud-stat-value text-white/40 text-base">{{ __('None') }}</div>
                <div class="ud-stat-sub">
                    <a href="{{ route('user.subscription.plans') }}" class="ud-card-link">{{ __('Browse plans') }} →</a>
                </div>
            @endif
        </div>

        {{-- Credits --}}
        <div class="ud-stat">
            <div class="ud-stat-icon bg-amber-500/10">
                <x-heroicon-o-sparkles class="h-4 w-4 text-amber-400" />
            </div>
            <div class="ud-stat-label">{{ __('Credits') }}</div>
            @if($isActive)
                <div class="ud-stat-value">{{ number_format($subscription->credits_remaining) }}</div>
                <div class="ud-stat-sub">{{ __('Remaining') }}</div>
            @else
                <div class="ud-stat-value text-white/25">—</div>
                <div class="ud-stat-sub">{{ __('No active plan') }}</div>
            @endif
        </div>

        {{-- Subscription end --}}
        <div class="ud-stat">
            <div class="ud-stat-icon bg-sky-500/10">
                <x-heroicon-o-calendar-days class="h-4 w-4 text-sky-400" />
            </div>
            <div class="ud-stat-label">{{ __('Renews') }}</div>
            @if($isActive)
                <div class="ud-stat-value text-base">{{ $subscription->current_period_end->format('M d') }}</div>
                <div class="ud-stat-sub">{{ $subscription->current_period_end->format('Y') }}</div>
            @else
                <div class="ud-stat-value text-white/25">—</div>
                <div class="ud-stat-sub">{{ __('No plan') }}</div>
            @endif
        </div>

    </div>

    {{-- Quick actions --}}
    <div>
        <p class="ud-stat-label mb-3">{{ __('Quick Actions') }}</p>
        <div class="ud-action-grid">
            <a href="{{ route('user.payment.create') }}" class="ud-action">
                <div class="ud-action-icon bg-emerald-500/10">
                    <x-heroicon-o-arrow-down-on-square class="h-4 w-4 text-emerald-400" />
                </div>
                <div>
                    <div class="ud-action-label">{{ __('Add Funds') }}</div>
                    <div class="ud-action-desc">{{ __('Top up wallet') }}</div>
                </div>
            </a>
            <a href="{{ route('user.subscription.plans') }}" class="ud-action">
                <div class="ud-action-icon bg-violet-500/10">
                    <x-heroicon-o-ticket class="h-4 w-4 text-violet-400" />
                </div>
                <div>
                    <div class="ud-action-label">{{ __('Plans') }}</div>
                    <div class="ud-action-desc">{{ __('Browse & upgrade') }}</div>
                </div>
            </a>
            <a href="{{ route('user.payout.create') }}" class="ud-action">
                <div class="ud-action-icon bg-amber-500/10">
                    <x-heroicon-o-arrow-up-tray class="h-4 w-4 text-amber-400" />
                </div>
                <div>
                    <div class="ud-action-label">{{ __('Payout') }}</div>
                    <div class="ud-action-desc">{{ __('Withdraw funds') }}</div>
                </div>
            </a>
            <a href="{{ route('user.support.create') }}" class="ud-action">
                <div class="ud-action-icon bg-sky-500/10">
                    <x-heroicon-o-lifebuoy class="h-4 w-4 text-sky-400" />
                </div>
                <div>
                    <div class="ud-action-label">{{ __('Support') }}</div>
                    <div class="ud-action-desc">{{ __('Open a ticket') }}</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent transactions --}}
    @php
        try {
            $payments = $user->payments()->latest()->limit(6)->get();
        } catch (\Exception $e) {
            $payments = collect([]);
        }
    @endphp

    <div class="ud-card">
        <div class="ud-card-header">
            <span class="ud-card-title">{{ __('Recent Payments') }}</span>
            <a href="{{ route('user.payment.index') }}" class="ud-card-link">{{ __('View all') }} →</a>
        </div>

        @if($payments->isEmpty())
            <div class="ud-empty">
                <x-heroicon-o-credit-card class="h-10 w-10" />
                <div class="ud-empty-title">{{ __('No payments yet') }}</div>
                <div class="ud-empty-body">{{ __('Your payment history will appear here.') }}</div>
            </div>
        @else
            @foreach($payments as $payment)
            @php
                $statusMap = [
                    'completed'         => 'ud-badge-green',
                    'awaiting_approval' => 'ud-badge-amber',
                    'failed'            => 'ud-badge-red',
                ];
                $badgeClass = $statusMap[$payment->status] ?? 'ud-badge-gray';
            @endphp
            <div class="ud-row">
                <div class="ud-row-icon">
                    <x-heroicon-o-arrow-down-on-square class="h-4 w-4" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="ud-row-label truncate">{{ $payment->gateway->name ?? __('Payment') }}</div>
                    <div class="ud-row-sub">{{ $payment->created_at->format('M d, Y') }}</div>
                </div>
                <div class="text-right">
                    <div class="ud-row-amount">{{ $payment->formattedAmount() }}</div>
                    <span class="ud-badge {{ $badgeClass }} mt-1">{{ __(str_replace('_', ' ', $payment->status)) }}</span>
                </div>
            </div>
            @endforeach
        @endif
    </div>

</div>

@push('scripts')
<script>
$(function () {
    'use strict';
    $(document).on('click', '[data-dismiss-nudge]', function () {
        var key  = $(this).data('dismiss-nudge');
        var $row = $(this).closest('[data-nudge]');
        $row.fadeOut(200, function () { $row.remove(); });
        $.post('{{ route('user.dashboard.dismiss-nudge') }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            key: key
        });
    });
});
</script>
@endpush

@endsection

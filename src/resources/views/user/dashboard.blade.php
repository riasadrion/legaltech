@extends('theme::layouts.user')

@section('content')
@php
    $user         = auth('user')->user();
    $subscription = $user->subscription;
    $isActive     = $subscription && $subscription->isActive();
    $balance      = $user->balance ?? 0;
    $currency     = setting('site.currency_symbol', '$');
@endphp

<div class="space-y-6">

    {{-- Welcome + hero --}}
    @php
        try { $recentPayment = $user->payments()->latest()->first(); } catch(\Throwable) { $recentPayment = null; }
        $pendingPayments = 0;
        try { $pendingPayments = $user->payments()->where('status','awaiting_approval')->count(); } catch(\Throwable) {}
    @endphp
    <section class="relative rounded-3xl glass overflow-hidden p-7 md:p-9">
        <div class="absolute -right-20 -top-20 w-80 h-80 glow-lime opacity-70"></div>
        <div class="absolute -left-10 -bottom-20 w-72 h-72 glow-violet opacity-40"></div>

        <div class="relative grid md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-7">
                <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-2">— {{ now()->format('l · d M Y') }}</div>
                <h1 class="font-display text-5xl md:text-6xl leading-[0.95] text-white">
                    {{ __('Welcome back,') }} <span class="italic-display">{{ $user->name ?? __('there') }}.</span>
                </h1>
                <p class="text-white/65 mt-3 max-w-md">
                    @if($isActive)
                        {{ __('You\'re on the') }} <span class="text-white">{{ $subscription->plan->name }}</span> {{ __('plan.') }}
                        @if($subscription->credits_remaining > 0)
                            {{ number_format($subscription->credits_remaining) }} {{ __('credits remaining.') }}
                        @endif
                    @elseif($pendingPayments > 0)
                        {{ __('You\'ve got') }} <span class="text-white">{{ $pendingPayments }} {{ __('payments awaiting approval') }}</span>.
                    @else
                        {{ __('Manage your account, billing, and subscription from here.') }}
                    @endif
                </p>

                {{-- AI prompt --}}
                <form method="GET" action="{{ route('user.contracts.ai.create') }}" class="mt-7 max-w-xl">
                    <div class="flex items-center gap-2 rounded-2xl bg-ink-900/70 border border-white/10 p-2 pl-4 focus-within:border-lime/40 transition">
                        <x-heroicon-o-sparkles class="h-4 w-4 text-lime shrink-0" />
                        <input name="brief" required minlength="20"
                               placeholder="{{ __('Generate a contract — e.g. \'NDA with Helix/Law, mutual, UK law\'') }}"
                               class="flex-1 bg-transparent outline-none text-[14px] placeholder:text-white/35 py-2 min-w-0 text-white">
                        <button type="submit" class="btn-lime rounded-xl px-4 py-2 text-[13px] flex items-center gap-1.5 shrink-0">
                            {{ __('Continue') }}
                        </button>
                    </div>
                    <p class="text-[11px] text-white/35 mt-2">
                        @if($user->canGenerateAiContract())
                            {{ __('Add parties and generate on the next step.') }}
                        @else
                            <x-heroicon-o-lock-closed class="inline h-3 w-3 -mt-0.5" />
                            {{ __('Available on') }} <a href="{{ route('user.subscription.plans') }}" class="text-lime hover:underline">{{ __('Pro plans') }}</a>.
                        @endif
                    </p>
                </form>

            </div>

            {{-- Right preview card --}}
            <div class="md:col-span-5 mt-2 md:mt-0">
                <div class="relative rounded-2xl bg-ink-900/70 border border-white/10 p-5">
                    @if($recentPayment)
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-mono text-white/45">{{ __('MOST RECENT') }}</div>
                        @php
                            $rStatusMap = ['completed' => 'pill-sign', 'awaiting_approval' => 'pill-review', 'failed' => 'pill-draft'];
                            $rPill = $rStatusMap[$recentPayment->status] ?? 'pill-done';
                        @endphp
                        <span class="pill {{ $rPill }}">{{ __(ucwords(str_replace('_',' ',$recentPayment->status))) }}</span>
                    </div>
                    <h3 class="font-display text-xl mt-1 text-white">{{ $recentPayment->gateway->name ?? __('Payment') }}</h3>
                    <p class="text-[12px] text-white/55 mt-1">{{ $recentPayment->created_at->format('M d, Y') }}</p>

                    <div class="mt-4">
                        <div class="text-[10px] font-mono text-white/40 uppercase tracking-widest">{{ __('Amount') }}</div>
                        <div class="text-3xl font-display text-white mt-1">{{ $recentPayment->formattedAmount() }}</div>
                    </div>

                    <div class="mt-5 flex items-center gap-2">
                        <a href="{{ route('user.payment.index') }}" class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                            <x-heroicon-o-queue-list class="h-4 w-4" />
                            {{ __('View all') }}
                        </a>
                        <a href="{{ route('user.payment.create') }}" class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            {{ __('Add funds') }}
                        </a>
                    </div>
                    @else
                    <div class="text-center py-6">
                        <div class="text-[11px] font-mono text-white/45 mb-3">{{ __('NO PAYMENTS YET') }}</div>
                        <a href="{{ route('user.payment.create') }}" class="btn-lime rounded-xl px-4 py-2 text-[13px] inline-flex items-center gap-1.5">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            {{ __('Add Funds') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

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

    {{-- Stats — match template card style --}}
    @php
        try { $totalPayments = $user->payments()->count(); } catch(\Throwable) { $totalPayments = 0; }
        try { $completedPayments = $user->payments()->where('status','completed')->count(); } catch(\Throwable) { $completedPayments = 0; }
    @endphp
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3">

        {{-- Wallet --}}
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Wallet') }}</span>
                <x-heroicon-o-banknotes class="h-4 w-4 text-white/45" />
            </div>
            <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ $currency }}{{ number_format($balance, 2) }}</div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px]">
                <a href="{{ route('user.payment.index') }}" class="text-lime hover:underline">{{ __('View history') }} →</a>
            </div>
            <svg viewBox="0 0 120 30" class="absolute right-2 bottom-2 w-20 h-7">
                <path class="spark" d="M0 22 Q 20 8 40 16 T 80 6 T 120 12"/>
            </svg>
        </div>

        {{-- Subscription --}}
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Plan') }}</span>
                <x-heroicon-o-square-3-stack-3d class="h-4 w-4 text-white/45" />
            </div>
            @if($isActive)
                <div class="font-display text-2xl md:text-3xl mt-2 text-white">{{ $subscription->plan->name }}</div>
                <div class="mt-2 flex items-center gap-1.5 text-[11px]">
                    <span class="pulse-dot inline-block w-1.5 h-1.5 rounded-full bg-lime"></span>
                    <span class="text-white/55">{{ __('Active') }}</span>
                </div>
            @else
                <div class="font-display text-3xl md:text-5xl mt-2 text-white/30">—</div>
                <div class="mt-2 text-[11px]">
                    <a href="{{ route('user.subscription.plans') }}" class="text-lime hover:underline">{{ __('Browse plans') }} →</a>
                </div>
            @endif
        </div>

        {{-- Credits --}}
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Credits') }}</span>
                <x-heroicon-o-sparkles class="h-4 w-4 text-white/45" />
            </div>
            @if($isActive && $subscription->credits_total > 0)
                <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ number_format($subscription->credits_remaining) }}</div>
                <div class="mt-2 flex items-end gap-0.5 h-6">
                    @php $pct = max(0, min(100, round(($subscription->credits_remaining / $subscription->credits_total) * 100))); @endphp
                    @for($bi = 1; $bi <= 7; $bi++)
                        @php $opacity = $bi / 7 <= $pct / 100 ? 'bg-lime' : 'bg-white/12'; $h = ['h-2','h-2','h-3','h-4','h-4','h-5','h-6'][$bi-1]; @endphp
                        <div class="w-1.5 {{ $opacity }} {{ $h }} rounded-sm"></div>
                    @endfor
                </div>
            @else
                <div class="font-display text-3xl md:text-5xl mt-2 text-white/25">—</div>
                <div class="mt-2 text-[11px] text-white/40">{{ __('No active plan') }}</div>
            @endif
        </div>

        {{-- Renews / Payments --}}
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Renews') }}</span>
                <x-heroicon-o-calendar-days class="h-4 w-4 text-white/45" />
            </div>
            @if($isActive)
                <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ $subscription->current_period_end->format('d') }}<span class="text-base text-white/45 align-middle"> {{ $subscription->current_period_end->format('M') }}</span></div>
                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-lime">
                    <x-heroicon-o-arrow-up class="h-3 w-3" /> {{ $subscription->current_period_end->format('Y') }}
                </div>
            @else
                <div class="font-display text-3xl md:text-5xl mt-2 text-white/25">—</div>
                <div class="mt-2 text-[11px] text-white/40">{{ __('No plan') }}</div>
            @endif
        </div>

    </section>

    {{-- Recent payments table --}}
    @php
        try { $payments = $user->payments()->latest()->limit(6)->get(); } catch (\Exception $e) { $payments = collect([]); }
    @endphp

    <section class="rounded-2xl glass overflow-hidden">
        <div class="px-5 pt-5 flex flex-wrap items-end justify-between gap-3 border-b border-white/8 pb-3">
            <div>
                <h2 class="font-display text-2xl text-white">{{ __('Recent Payments') }}</h2>
                <div class="text-[12px] text-white/50 mt-0.5">{{ $payments->count() }} {{ __('transactions') }}</div>
            </div>
            <a href="{{ route('user.payment.index') }}" class="text-[13px] text-lime hover:underline">{{ __('View all') }} →</a>
        </div>

        @if($payments->isEmpty())
            <div class="px-5 py-10 text-center text-white/45 text-sm">
                {{ __('No payments yet — add funds to get started.') }}
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="text-[10px] font-mono uppercase tracking-widest text-white/40">
                    <tr class="border-b border-white/6">
                        <th class="text-left font-medium px-5 py-3">{{ __('Payment') }}</th>
                        <th class="text-left font-medium py-3 hidden sm:table-cell">{{ __('Gateway') }}</th>
                        <th class="text-left font-medium py-3">{{ __('Amount') }}</th>
                        <th class="text-left font-medium py-3 hidden sm:table-cell">{{ __('Status') }}</th>
                        <th class="text-right font-medium py-3 pr-5 hidden md:table-cell">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($payments as $payment)
                    @php
                        $pillMap = ['completed' => 'pill-sign', 'awaiting_approval' => 'pill-review', 'failed' => 'pill-draft'];
                        $pPill = $pillMap[$payment->status] ?? 'pill-done';
                    @endphp
                    <tr class="hover:bg-white/3 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-lg bg-white/4 border border-white/8 grid place-items-center">
                                    <x-heroicon-o-credit-card class="h-4 w-4 text-white/65" />
                                </span>
                                <div class="font-mono text-[11px] text-white/35">#{{ $payment->id }}</div>
                            </div>
                        </td>
                        <td class="py-3.5 text-white/70 hidden sm:table-cell">{{ $payment->gateway->name ?? __('—') }}</td>
                        <td class="py-3.5 font-mono text-white/85">{{ $payment->formattedAmount() }}</td>
                        <td class="py-3.5 hidden sm:table-cell">
                            <span class="pill {{ $pPill }}">{{ __(ucwords(str_replace('_',' ',$payment->status))) }}</span>
                        </td>
                        <td class="py-3.5 pr-5 text-right text-white/55 hidden md:table-cell">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/8 flex items-center justify-between text-[12px] text-white/50">
            <span>{{ __('Showing') }} <span class="text-white">{{ $payments->count() }}</span> {{ __('payments') }}</span>
            <a href="{{ route('user.payment.index') }}" class="text-lime hover:underline">{{ __('See all') }} →</a>
        </div>
        @endif
    </section>

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

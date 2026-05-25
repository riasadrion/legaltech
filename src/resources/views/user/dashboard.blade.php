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
                        @if($subscription->plan->ai_contract_generation)
                            @if($subscription->plan->ai_drafts > 0)
                                {{ number_format($subscription->ai_drafts_remaining) }} {{ __('AI drafts remaining.') }}
                            @else
                                {{ __('Unlimited AI drafts.') }}
                            @endif
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

            {{-- Right contract card --}}
            <div class="md:col-span-5 mt-2 md:mt-0">
                <div class="relative rounded-2xl bg-ink-900/70 border border-white/10 p-5">
                    @if($recentContract)
                        @php
                            $pillMap = [
                                'draft'      => 'pill-draft',
                                'generating' => 'pill-draft',
                                'editing'    => 'pill-draft',
                                'ready'      => 'pill-done',
                                'sent'       => 'pill-review',
                                'signed'     => 'pill-sign',
                                'expired'    => 'pill-draft',
                            ];
                            $pill = $pillMap[$recentContract->status] ?? 'pill-done';
                            $signers = $recentContract->signers;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="text-[11px] font-mono text-white/45">{{ __('MOST RECENT') }}</div>
                            <span class="pill {{ $pill }}">{{ __(ucfirst($recentContract->status)) }}</span>
                        </div>
                        <h3 class="font-display text-xl mt-1 text-white truncate">{{ $recentContract->title }}</h3>
                        <p class="text-[12px] text-white/55 mt-1">{{ $recentContract->created_at->format('M d, Y') }}</p>

                        @if($signers->count() > 0)
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            @foreach($signers->take(2) as $signer)
                            <div>
                                <div class="text-[10px] font-mono text-white/40 uppercase tracking-widest">{{ $signer->name }}</div>
                                @if($signer->signed_at)
                                    <svg viewBox="0 0 200 50" class="h-9 w-full">
                                        <path d="M5 35 C 20 5, 35 50, 55 25 S 95 5, 115 30 S 160 50, 195 15"
                                              fill="none" stroke="#D4FF3A" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    <div class="text-[10px] text-white/50 mt-1">{{ __('signed') }}</div>
                                @else
                                    <div class="h-9 border-b border-dashed border-white/20 flex items-end">
                                        <span class="text-[10px] font-mono text-white/35 pb-1">{{ __('awaiting…') }}</span>
                                    </div>
                                    <div class="text-[10px] text-white/50 mt-1">{{ __('pending') }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="mt-5 flex items-center gap-2">
                            <a href="{{ route('user.contracts.show', $recentContract) }}"
                               class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                                <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                                {{ __('Open') }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-[11px] font-mono text-white/45 mb-3">{{ __('NO CONTRACTS YET') }}</div>
                            <a href="{{ route('user.contracts.index') }}"
                               class="btn-lime rounded-xl px-4 py-2 text-[13px] inline-flex items-center gap-1.5">
                                {{ __('Create contract') }}
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
            <x-heroicon-o-information-circle class="ud-nudge-icon h-4 w-4 shrink-0" />
            <span class="flex-1 min-w-0 truncate ud-nudge-title">{{ $nudge['title'] }}</span>
            <a href="{{ $nudge['action'] }}" class="ud-nudge-action shrink-0">{{ $nudge['label'] }} →</a>
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
            @if($isActive && $subscription->plan->credits > 0)
                <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ number_format($subscription->credits_remaining) }}</div>
                <div class="mt-2 flex items-end gap-0.5 h-6">
                    @php $pct = max(0, min(100, round(($subscription->credits_remaining / $subscription->plan->credits) * 100))); @endphp
                    @for($bi = 1; $bi <= 7; $bi++)
                        @php $opacity = $bi / 7 <= $pct / 100 ? 'bg-lime' : 'bg-white/12'; $h = ['h-2','h-2','h-3','h-4','h-4','h-5','h-6'][$bi-1]; @endphp
                        <div class="w-1.5 {{ $opacity }} {{ $h }} rounded-sm"></div>
                    @endfor
                </div>
                @if($subscription->plan->ai_contract_generation)
                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-white/40">
                    <x-heroicon-o-sparkles class="h-3.5 w-3.5 text-lime/60" />
                    <span>{{ $subscription->ai_drafts_remaining }} / {{ $subscription->plan->ai_drafts }} {{ __('AI drafts') }}</span>
                </div>
                @endif
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

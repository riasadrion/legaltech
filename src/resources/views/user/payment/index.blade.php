@extends('theme::layouts.user')

@section('breadcrumb', __('Payments'))

@section('content')
@php $currency = setting('site.currency_symbol', '$'); @endphp
<div class="space-y-6">

    {{-- Page heading --}}
    <div>
        <h1 class="ud-page-title">{{ __('Payments') }}</h1>
        <p class="ud-page-sub">{{ __('Your wallet balance and payment history.') }}</p>
    </div>

    {{-- Cards row: wallet + gateways --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

        {{-- Wallet balance card --}}
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ __('Wallet') }}</span>
                <x-heroicon-o-banknotes class="h-6 w-6 text-white/45" />
            </div>
            <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ $balance->format() }}</div>
            <div class="mt-2 text-[11px]">
                <a href="{{ route('user.payment.create') }}" class="text-lime hover:underline">{{ __('Add funds') }} →</a>
            </div>
            <svg viewBox="0 0 120 30" class="absolute right-2 bottom-2 w-20 h-7">
                <path class="spark" d="M0 22 Q 20 8 40 16 T 80 6 T 120 12"/>
            </svg>
        </div>

        {{-- Gateway cards --}}
        @foreach($gateways as $gateway)
        @php $totalSpent = $gatewayTotals[$gateway->id] ?? 0; $logo = $gateway->logoUrl(); @endphp
        <div class="rounded-2xl glass p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-widest text-white/45">{{ $gateway->name }}</span>
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $gateway->name }}" class="h-6 w-6 rounded-lg object-contain opacity-70">
                @else
                    <x-heroicon-o-credit-card class="h-6 w-6 text-white/45" />
                @endif
            </div>
            @if($totalSpent > 0)
            <div class="font-display text-3xl md:text-5xl mt-2 text-white">{{ $currency }}{{ number_format((float) $totalSpent, 2) }}</div>
            <div class="mt-2 text-[11px] text-white/40">{{ __('Total spent') }}</div>
            @else
            <div class="font-display text-3xl md:text-5xl mt-2 text-white/25">—</div>
            <div class="mt-2 text-[11px] text-white/30">{{ __('No payments yet') }}</div>
            @endif
        </div>
        @endforeach

    </div>

    {{-- Recent payments table --}}
    @if($payments->isNotEmpty())
    <section class="rounded-2xl glass overflow-hidden">
        <div class="px-5 pt-5 flex flex-wrap items-end justify-between gap-3 border-b border-white/8 pb-3">
            <div>
                <h2 class="font-display text-2xl text-white">{{ __('Recent payments') }}</h2>
                <div class="text-[12px] text-white/50 mt-0.5">{{ $payments->total() }} {{ __('transactions') }}</div>
            </div>
            <a href="{{ route('user.payment.create') }}" class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                <x-heroicon-o-plus class="h-4 w-4" />
                {{ __('Add funds') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="text-[10px] font-mono uppercase tracking-widest text-white/40">
                    <tr class="border-b border-white/6">
                        <th class="text-left font-medium px-5 py-3">{{ __('Date') }}</th>
                        <th class="text-left font-medium py-3 hidden sm:table-cell">{{ __('Method') }}</th>
                        <th class="text-left font-medium py-3">{{ __('Amount') }}</th>
                        <th class="text-left font-medium py-3">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($payments as $payment)
                    @php
                        $pillMap = ['completed' => 'pill-sign', 'awaiting_approval' => 'pill-review', 'failed' => 'pill-draft'];
                        $pPill = $pillMap[$payment->status] ?? 'pill-done';
                    @endphp
                    <tr>
                        <td class="px-5 py-3.5 text-white/55 whitespace-nowrap">{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3.5 text-white/70 hidden sm:table-cell">{{ $payment->gateway->name }}</td>
                        <td class="py-3.5 font-mono text-white/85">{{ $payment->formattedAmount() }}</td>
                        <td class="py-3.5">
                            <span class="pill {{ $pPill }}">{{ __(ucfirst(str_replace('_', ' ', $payment->status))) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-5 py-3 border-t border-white/8 text-xs text-white/45">
            {{ $payments->links() }}
        </div>
        @endif
    </section>
    @else
    <div class="rounded-2xl glass p-12 text-center text-white/40">
        <x-heroicon-o-credit-card class="h-10 w-10 mx-auto mb-3 opacity-30" />
        <div class="text-sm">{{ __('No payments yet — add funds to get started.') }}</div>
        <a href="{{ route('user.payment.create') }}" class="btn-lime rounded-lg px-4 py-2 text-[13px] inline-flex items-center gap-1.5 mt-4">
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ __('Add Funds') }}
        </a>
    </div>
    @endif

</div>
@endsection

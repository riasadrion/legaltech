@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Payment funds') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('Choose a payment method to top up your wallet.') }}</p>
    </div>

    @if($gateways->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-10 text-center shadow-sm">
        <p class="text-sm text-gray-500">{{ __('No payment methods are currently available. Please check back later.') }}</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($gateways as $gateway)
        <a href="{{ route('user.payment.create', ['gateway' => $gateway->driver, 'plan_id' => request('plan_id')]) }}"
           class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:border-brand-400 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 group-hover:bg-brand-100">
                    <x-heroicon-o-credit-card class="h-5 w-5" />
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $gateway->name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ __('Min :amount', ['amount' => $gateway->formatAmount($gateway->min_amount)]) }}
                        @if($gateway->fee_flat > 0 || $gateway->fee_percent > 0)
                            · {{ __('Fee applies') }}
                        @else
                            · {{ __('No fee') }}
                        @endif
                    </p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Recent payments --}}
    @if($payments->isNotEmpty())
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Recent payments') }}</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Date') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Method') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Amount') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($payments as $payment)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $payment->gateway->name }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $payment->formattedAmount() }}</td>
                    <td class="px-5 py-3">
                        @php $sc = match($payment->status) { 'completed' => 'bg-green-100 text-green-700', 'awaiting_approval' => 'bg-amber-100 text-amber-700', 'failed' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-500' }; @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ __(ucfirst(str_replace('_', ' ', $payment->status))) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($payments->hasPages())
        <div class="border-t border-gray-100 px-5 py-3">{{ $payments->links() }}</div>
        @endif
    </div>
    @endif

</div>
@endsection

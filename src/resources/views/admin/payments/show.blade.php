@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.payments.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Payment #{{ $payment->id }}</h1>
            <p class="text-sm text-gray-400">{{ formatDate($payment->created_at) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">Payment details</h3>
            </div>
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">User</dt><dd class="font-medium text-gray-900">{{ $payment->user->email }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Gateway</dt><dd class="text-gray-700">{{ $payment->gateway->name }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Amount</dt><dd class="font-semibold text-gray-900">{{ $payment->formattedAmount() }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Fee</dt><dd class="text-gray-700">{{ $payment->formattedFee() }}</dd></div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Net credited</dt>
                    <dd class="font-semibold text-green-700">${{ number_format($payment->netAmount(), 2) }}</dd>
                </div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Currency</dt><dd class="text-gray-700">{{ $payment->currency }}</dd></div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Status</dt>
                    <dd>
                        @php
                            $sc = match($payment->status) {
                                'completed'         => 'bg-green-100 text-green-700',
                                'awaiting_approval' => 'bg-amber-100 text-amber-700',
                                'failed'            => 'bg-red-100 text-red-700',
                                default             => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                        </span>
                    </dd>
                </div>
                @if($payment->gateway_ref)
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Gateway ref</dt><dd class="font-mono text-xs text-gray-600">{{ $payment->gateway_ref }}</dd></div>
                @endif
                @if($payment->transaction_id)
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Transaction</dt><dd class="text-gray-700">#{{ $payment->transaction_id }}</dd></div>
                @endif
            </dl>
        </div>


        <div class="space-y-5">
            @if($payment->receipt_path)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900">Receipt</h3>
                </div>
                <div class="px-5 py-4">
                    <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <x-heroicon-o-document-arrow-down class="h-4 w-4" />
                        View receipt
                    </a>
                </div>
            </div>
            @endif

            @if($payment->isAwaitingApproval())
            <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm overflow-hidden">
                <div class="border-b border-amber-100 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-amber-900">Review</h3>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                        @csrf
                        <button type="submit"
                                data-confirm="Approve this payment and credit the user's wallet?"
                                class="w-full rounded-lg bg-green-600 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Approve &amp; credit wallet
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="space-y-2">
                        @csrf
                        <textarea name="rejection_reason" rows="2" placeholder="Reason for rejection…" required
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"></textarea>
                        <button type="submit"
                                data-confirm="Reject this payment?"
                                class="w-full rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($payment->reviewed_by)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900">Review record</h3>
                </div>
                <dl class="divide-y divide-gray-100 text-sm">
                    <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Reviewed by</dt><dd class="text-gray-700">{{ $payment->reviewer->email }}</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">Reviewed at</dt><dd class="text-gray-700">{{ formatDate($payment->reviewed_at) }}</dd></div>
                    @if($payment->rejection_reason)
                    <div class="px-5 py-3"><dt class="text-xs text-gray-500 mb-1">Rejection reason</dt><dd class="text-sm text-gray-700">{{ $payment->rejection_reason }}</dd></div>
                    @endif
                </dl>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

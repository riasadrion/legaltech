@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.payments.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Payment #{{ $payment->id }}</h1>
            <p class="text-sm text-gray-400 dark:text-slate-500">{{ formatDate($payment->created_at) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

        {{-- Payment details --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Payment details</h3>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">User</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $payment->user->email }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Gateway</dt><dd class="text-gray-700 dark:text-slate-300">{{ $payment->gateway->name }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Amount</dt><dd class="font-semibold text-gray-900 dark:text-white">{{ $payment->formattedAmount() }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Fee</dt><dd class="text-gray-700 dark:text-slate-300">{{ $payment->formattedFee() }}</dd></div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Net credited</dt>
                    <dd class="font-semibold text-green-700 dark:text-green-400">${{ number_format($payment->netAmount(), 2) }}</dd>
                </div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Currency</dt><dd class="text-gray-700 dark:text-slate-300">{{ $payment->currency }}</dd></div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Status</dt>
                    <dd>
                        @php
                            $sc = match($payment->status) {
                                'completed'         => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                                'awaiting_approval' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                'failed'            => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                                default             => 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                        </span>
                    </dd>
                </div>
                @if($payment->gateway_ref)
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Gateway ref</dt><dd class="font-mono text-xs text-gray-600 dark:text-slate-300">{{ $payment->gateway_ref }}</dd></div>
                @endif
                @if($payment->transaction_id)
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Transaction</dt><dd class="text-gray-700 dark:text-slate-300">#{{ $payment->transaction_id }}</dd></div>
                @endif
            </dl>
        </div>

        {{-- Right column --}}
        <div class="space-y-5">

            {{-- Receipt --}}
            @if($payment->receipt_path)
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Receipt</h3>
                </div>
                <div class="px-5 py-4">
                    <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                        <x-heroicon-o-document-arrow-down class="h-4 w-4" />
                        View receipt
                    </a>
                </div>
            </div>
            @endif

            {{-- Review actions --}}
            @if($payment->isAwaitingApproval())
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Review</h3>
                </div>
                <div class="px-5 py-4 space-y-3" x-data="{ showReject: false }">

                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                        @csrf
                        <button type="submit"
                                data-confirm="Approve this payment and credit the user's wallet?"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors">
                            <x-heroicon-o-check-circle class="h-4 w-4" />
                            Approve &amp; credit wallet
                        </button>
                    </form>

                    <div>
                        <button type="button" x-show="!showReject" @click="showReject = true"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-red-300 dark:border-red-800 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/20 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 transition-colors">
                            <x-heroicon-o-x-circle class="h-4 w-4" />
                            Reject
                        </button>

                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" x-show="showReject" class="space-y-2">
                            @csrf
                            <textarea name="rejection_reason" rows="3"
                                      placeholder="Reason for rejection…" required
                                      class="w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400 transition-colors"></textarea>
                            <div class="flex gap-2">
                                <button type="button" @click="showReject = false"
                                        class="flex-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                        data-confirm="Reject this payment?"
                                        class="flex-1 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                                    Confirm rejection
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            @endif

            {{-- Review record --}}
            @if($payment->reviewed_by)
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Review record</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                    <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Reviewed by</dt><dd class="text-gray-700 dark:text-slate-300">{{ $payment->reviewer->email }}</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500 dark:text-slate-400">Reviewed at</dt><dd class="text-gray-700 dark:text-slate-300">{{ formatDate($payment->reviewed_at) }}</dd></div>
                    @if($payment->rejection_reason)
                    <div class="px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-slate-400 mb-1">Rejection reason</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $payment->rejection_reason }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

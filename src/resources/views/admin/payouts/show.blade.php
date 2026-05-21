@extends('admin.layouts.app')

@section('title', 'Payout #{{ $payout->id }}')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.payouts.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">Payout #{{ $payout->id }}</h1>
        @php $sc = match($payout->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-gray-100 text-gray-500', default => 'bg-amber-100 text-amber-700' }; @endphp
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst($payout->status) }}</span>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5"><h2 class="text-sm font-semibold text-gray-900">Summary</h2></div>
        <dl class="divide-y divide-gray-100 px-5 text-sm">
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">User</dt><dd class="font-medium text-gray-900">{{ $payout->user->name }} ({{ $payout->user->email }})</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Method</dt><dd class="text-gray-700">{{ $payout->method->getLocaleName() }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Amount</dt><dd class="font-semibold text-gray-900">{{ $payout->formattedAmount() }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Fee</dt><dd class="text-gray-700">{{ $payout->formattedFee() }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Net payout</dt><dd class="font-semibold text-gray-900">${{ number_format($payout->netAmount(), 2) }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Submitted</dt><dd class="text-gray-500">{{ formatDate($payout->created_at) }}</dd></div>
        </dl>
    </div>

    @if(!empty($payout->payout_details))
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5"><h2 class="text-sm font-semibold text-gray-900">Payout details</h2></div>
        <dl class="divide-y divide-gray-100 px-5 text-sm">
            @foreach($payout->payout_details as $key => $value)
            <div class="flex justify-between py-2.5">
                <dt class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                <dd class="font-medium text-gray-900">{{ $value }}</dd>
            </div>
            @endforeach
        </dl>
    </div>
    @endif

    @if($payout->transaction)
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5"><h2 class="text-sm font-semibold text-gray-900">Transaction</h2></div>
        <dl class="divide-y divide-gray-100 px-5 text-sm">
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Transaction #</dt><dd class="font-medium text-gray-900">{{ $payout->transaction_id }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Processed by</dt><dd class="text-gray-700">{{ $payout->reviewer?->name }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Processed at</dt><dd class="text-gray-500">{{ formatDate($payout->reviewed_at) }}</dd></div>
        </dl>
    </div>
    @endif

    @if($payout->isPending())
    @if(auth('admin')->user()->can('payments.manage'))
    <div class="flex gap-3">
        <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}" class="flex-1">
            @csrf
            <button type="submit"
                    class="w-full rounded-lg bg-green-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                Approve &amp; debit wallet
            </button>
        </form>

        <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}" class="flex-1" x-data="{ show: false }">
            @csrf
            <div x-show="!show">
                <button type="button" @click="show = true"
                        class="w-full rounded-lg border border-red-300 bg-white py-2.5 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-50">
                    Reject
                </button>
            </div>
            <div x-show="show" class="space-y-2">
                <textarea name="reason" rows="2" placeholder="Rejection reason (required)"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400" required></textarea>
                <button type="submit"
                        class="w-full rounded-lg bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Confirm rejection
                </button>
            </div>
        </form>
    </div>
    @endif
    @elseif($payout->isRejected() && $payout->rejection_reason)
    <p class="text-sm text-red-600">Rejection reason: {{ $payout->rejection_reason }}</p>
    @endif
</div>
@endsection

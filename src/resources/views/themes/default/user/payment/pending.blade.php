@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-lg px-4 py-16 text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
        <x-heroicon-o-clock class="h-8 w-8 text-amber-600" />
    </div>
    <h1 class="mt-5 text-2xl font-bold text-gray-900">Payment pending</h1>
    <p class="mt-2 text-sm text-gray-500">
        @if($payment->gateway->driver === 'manual')
            Your receipt has been submitted. An admin will review and approve your payment shortly.
        @else
            Your payment is being processed. This page will update once confirmed.
        @endif
    </p>
    <div class="mt-6 rounded-xl border border-gray-200 bg-white px-6 py-4 text-left shadow-sm">
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Payment #</dt><dd class="font-medium text-gray-900">{{ $payment->id }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Amount</dt><dd class="font-semibold text-gray-900">{{ $payment->formattedAmount() }}</dd></div>
            <div class="flex justify-between py-2.5"><dt class="text-gray-500">Method</dt><dd class="text-gray-700">{{ $payment->gateway->name }}</dd></div>
        </dl>
    </div>
    <a href="{{ route('user.payment.index') }}" class="mt-6 inline-flex items-center text-sm text-brand-600 hover:underline">
        Back to payments
    </a>
</div>
@endsection

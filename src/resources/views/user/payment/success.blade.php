@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-lg px-4 py-16 text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
        <x-heroicon-s-check-circle class="h-8 w-8 text-green-600" />
    </div>
    <h1 class="mt-5 text-2xl font-bold text-gray-900">Payment successful!</h1>
    <p class="mt-2 text-sm text-gray-500">
        {{ $payment->formattedAmount() }} has been added to your wallet.
    </p>
    <div class="mt-6 flex justify-center gap-3">
        <a href="{{ route('user.wallet.index') }}"
           class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            View wallet
        </a>
        <a href="{{ route('user.payment.index') }}"
           class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
            Payment again
        </a>
    </div>
</div>
@endsection

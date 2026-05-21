@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payouts</h1>
            <p class="mt-1 text-sm text-gray-500">Balance: <strong>{{ $balance->format() }}</strong></p>
        </div>
        @if(auth('user')->user()->kyc_status === 'approved')
        <a href="{{ route('user.payout.create') }}"
           class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            New payout
        </a>
        @else
        <a href="{{ route('user.kyc.index') }}"
           class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">
            Complete KYC to request payouts
        </a>
        @endif
    </div>

    @if($payouts->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-10 text-center shadow-sm">
        <p class="text-sm text-gray-500">No payouts yet.</p>
    </div>
    @else
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Method</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($payouts as $w)
                @php $sc = match($w->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-gray-100 text-gray-500', default => 'bg-amber-100 text-amber-700' }; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $w->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $w->method->getLocaleName() }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $w->formattedAmount() }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst($w->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($w->isPending())
                        <form method="POST" action="{{ route('user.payout.cancel', $w) }}" data-confirm="Cancel this payout request?">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:underline">Cancel</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($payouts->hasPages())
        <div class="border-t border-gray-100 px-5 py-3">{{ $payouts->links() }}</div>
        @endif
    </div>
    @endif

</div>
@endsection

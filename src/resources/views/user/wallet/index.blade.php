@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">

    {{-- Balance card --}}
    <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-700 p-6 text-white shadow-lg">
        <p class="text-sm font-medium text-brand-200">{{ __('Available balance') }}</p>
        <p class="mt-1 text-4xl font-bold tracking-tight">{{ $balance->format() }}</p>
        <p class="mt-3 text-xs text-brand-300">{{ $balance->currency }} &middot; {{ __('updated in real time') }}</p>
    </div>

    {{-- Filter + table --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Transaction history') }}</h2>
            <form method="GET" class="flex items-center gap-2">
                <select name="type" onchange="this.form.submit()"
                        class="h-8 rounded-lg border border-gray-300 px-2 text-xs shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">{{ __('All types') }}</option>
                    <option value="credit"       @selected(request('type') === 'credit')>{{ __('Credit') }}</option>
                    <option value="debit"        @selected(request('type') === 'debit')>{{ __('Debit') }}</option>
                    <option value="adjustment"   @selected(request('type') === 'adjustment')>{{ __('Adjustment') }}</option>
                    <option value="fee"          @selected(request('type') === 'fee')>{{ __('Fee') }}</option>
                    <option value="refund"       @selected(request('type') === 'refund')>{{ __('Refund') }}</option>
                    <option value="transfer_in"  @selected(request('type') === 'transfer_in')>{{ __('Transfer In') }}</option>
                    <option value="transfer_out" @selected(request('type') === 'transfer_out')>{{ __('Transfer Out') }}</option>
                </select>
                @if(request('type'))
                    <a href="{{ route('user.wallet.index') }}" class="text-xs text-gray-400 hover:text-gray-600">{{ __('Clear') }}</a>
                @endif
            </form>
        </div>

        @if($transactions->isEmpty())
        <div class="py-16 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" />
                </svg>
            </div>
            <p class="mt-3 text-sm font-medium text-gray-700">{{ __('No transactions yet') }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ __('Your transaction history will appear here.') }}</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/60 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Date') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Description') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Type') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Amount') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($transactions as $tx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                        {{ $tx->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-700 max-w-xs truncate">
                        {{ $tx->description ?: '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        @php
                            $typeClasses = match($tx->type) {
                                'credit', 'transfer_in', 'refund' => 'bg-green-100 text-green-700',
                                'debit', 'transfer_out', 'fee'    => 'bg-red-100 text-red-700',
                                'adjustment'                       => 'bg-blue-100 text-blue-700',
                                default                            => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeClasses }}">
                            {{ __(ucfirst(str_replace('_', ' ', $tx->type))) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold whitespace-nowrap {{ $tx->isCredit() ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tx->formattedAmount() }}
                    </td>
                    <td class="px-5 py-3.5 text-right text-xs text-gray-500 whitespace-nowrap">
                        {{ $tx->formattedBalance() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($transactions->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $transactions->firstItem(), 'last' => $transactions->lastItem(), 'total' => number_format($transactions->total())]) }}</span>
            {{ $transactions->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection

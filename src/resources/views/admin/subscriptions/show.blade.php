@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.subscriptions.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">@lang('Subscription') #{{ $subscription->id }}</h1>
            <p class="text-sm text-gray-400">{{ formatDate($subscription->created_at) }}</p>
        </div>
        @if(!$subscription->isExpired())
        <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}" :data-confirm="__('Cancel this subscription?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
                <x-heroicon-o-x-circle class="h-4 w-4" />
                @lang('Cancel subscription')
            </button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('Details')</h3>
            </div>
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">@lang('Status')</dt>
                    <dd>
                        @php
                            $sc = match($subscription->status) {
                                'active'    => 'bg-green-100 text-green-700',
                                'past_due'  => 'bg-amber-100 text-amber-700',
                                'cancelled' => 'bg-gray-100 text-gray-500',
                                'expired'   => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Plan')</dt><dd class="font-semibold text-gray-900">{{ $subscription->plan->name ?? '—' }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Billing cycle')</dt><dd class="text-gray-700">{{ ucfirst($subscription->plan->billing_cycle ?? '—') }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Credits remaining')</dt><dd class="font-semibold text-gray-900">{{ number_format($subscription->credits_remaining) }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Period start')</dt><dd class="text-gray-700">{{ formatDate($subscription->current_period_start, false) ?? '—' }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Period end')</dt><dd class="text-gray-700">{{ formatDate($subscription->current_period_end, false) ?? '—' }}</dd></div>
                @if($subscription->cancelled_at)
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Cancelled at')</dt><dd class="text-gray-700">{{ formatDate($subscription->cancelled_at) }}</dd></div>
                @endif
            </dl>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('User')</h3>
            </div>
            <div class="px-5 py-4 space-y-1">
                <div class="font-medium text-gray-900">{{ $subscription->user->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $subscription->user->email }}</div>
                <a href="{{ route('admin.users.show', $subscription->user) }}" class="mt-2 inline-flex items-center gap-1 text-xs text-brand-600 hover:text-brand-700 font-medium">
                    @lang('View user') →
                </a>
            </div>

            @if($subscription->payment)
            <div class="border-t border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">@lang('Initial payment')</h3>
            </div>
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Amount')</dt><dd class="font-semibold text-gray-900">{{ $subscription->payment->formattedAmount() }}</dd></div>
                <div class="flex justify-between px-5 py-3"><dt class="text-xs text-gray-500">@lang('Gateway')</dt><dd class="text-gray-700">{{ $subscription->payment->gateway->name ?? '—' }}</dd></div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">@lang('Status')</dt>
                    <dd><span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ ucfirst($subscription->payment->status) }}</span></dd>
                </div>
                <div class="px-5 py-3">
                    <a href="{{ route('admin.payments.show', $subscription->payment) }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">@lang('View payment') →</a>
                </div>
            </dl>
            @endif
        </div>
    </div>


    @if($subscription->creditLogs->count() > 0)
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h3 class="text-sm font-semibold text-gray-900">@lang('Credit history')</h3>
            <p class="mt-0.5 text-xs text-gray-500">@lang('Recent credit transactions for this subscription.')</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('When')</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Description')</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Amount')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($subscription->creditLogs as $log)
                <tr>
                    <td class="px-5 py-2.5 text-xs text-gray-500 whitespace-nowrap">{{ formatDate($log->created_at) }}</td>
                    <td class="px-5 py-2.5 text-gray-700">{{ $log->description }}</td>
                    <td class="px-5 py-2.5 text-right font-semibold {{ $log->amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $log->amount > 0 ? '+' : '' }}{{ number_format($log->amount) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

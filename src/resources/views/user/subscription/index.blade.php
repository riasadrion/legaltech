@extends('theme::layouts.user')

@section('title', __('My Subscription'))

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('My Subscription') }}</h1>

    {{-- Current subscription card --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        @if($subscription && $subscription->isActive())
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $subscription->plan->name }}</h2>
                        @php
                            $sc = match($subscription->status) {
                                'active' => 'bg-green-100 text-green-700',
                                'trial' => 'bg-blue-100 text-blue-700',
                                'past_due' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="inline-flex mt-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ __(ucfirst(str_replace('_', ' ', $subscription->status))) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">{{ $subscription->plan->formattedPrice() }}</p>
                        <p class="text-xs text-gray-500">/ {{ $subscription->plan->billing_cycle }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @if($subscription->plan->hasCreditLimit())
                    <div>
                        <p class="text-xs text-gray-500">{{ __('Credits remaining') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($subscription->credits_remaining) }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-500">{{ $subscription->isTrialing() ? __('Trial ends') : __('Period ends') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $subscription->current_period_end?->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">{{ __('Days remaining') }}</p>
                        <p class="text-lg font-semibold {{ $subscription->daysRemaining() <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ max(0, $subscription->daysRemaining()) }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('user.subscription.plans') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                        {{ __('View Plans') }}
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ __('No active subscription') }}</h2>
                <p class="text-gray-500 mb-6">{{ __('Choose a plan to get started') }}</p>
                <a href="{{ route('user.subscription.plans') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    {{ __('View Plans') }}
                </a>
            </div>
        @endif
    </div>

    {{-- Credit history (only if plan has credits) --}}
    @if($subscription?.plan->hasCreditLimit())
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Credit history') }}</h2>

        @if($creditHistory->isEmpty())
            <p class="text-center text-gray-400 py-8">{{ __('No credit transactions yet.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="divide-x divide-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">{{ __('Amount') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($creditHistory as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->amount >= 0 ? '+' : '' }}{{ $log->amount }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $creditHistory->links() }}
        @endif
    </div>
    @endif
</div>
@endsection

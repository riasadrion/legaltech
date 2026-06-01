@extends('theme::layouts.user')

@section('breadcrumb', __('Subscription'))

@section('content')
<div class="space-y-6">


    <div>
        <h1 class="ud-page-title">@lang('My Subscription')</h1>
        <p class="ud-page-sub">@lang('Manage your subscription and view your plan details.')</p>
    </div>


    <div class="ud-card">
        @if($subscription && $subscription->isActive())
            <div class="space-y-6">

                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="ud-card-title text-xl">{{ $subscription->plan->name }}</h2>
                        @php
                            $sc = match($subscription->status) {
                                'active' => 'ud-badge-green',
                                'past_due' => 'ud-badge-amber',
                                default => 'ud-badge-gray',
                            };
                        @endphp
                        <div class="mt-2">
                            <span class="ud-badge {{ $sc }}">
                                {{ __(ucfirst(str_replace('_', ' ', $subscription->status))) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-lime">{{ $subscription->plan->formattedPrice() }}</p>
                        <p class="text-xs text-white/40 mt-1">/ {{ $subscription->plan->billing_cycle }}</p>
                    </div>
                </div>

                <div class="border-t border-white/8"></div>


                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @if($subscription->plan->hasCreditLimit())
                    <div>
                        <p class="ud-stat-label">@lang('Credits remaining')</p>
                        <p class="text-xl font-semibold text-white mt-2">{{ number_format($subscription->credits_remaining) }}</p>
                        <p class="text-xs text-white/30 mt-0.5">@lang('of') {{ number_format($subscription->plan->credits) }}</p>
                    </div>
                    @endif

                    @if($subscription->plan->ai_contract_generation)
                    <div>
                        <p class="ud-stat-label">@lang('AI drafts remaining')</p>
                        <p class="text-xl font-semibold text-lime mt-2">{{ number_format($subscription->ai_drafts_remaining) }}</p>
                        <p class="text-xs text-white/30 mt-0.5">@lang('of') {{ number_format($subscription->plan->ai_drafts) }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="ud-stat-label">@lang('Period ends')</p>
                        <p class="text-xl font-semibold text-white mt-2">{{ $subscription->current_period_end?->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="ud-stat-label">@lang('Days remaining')</p>
                        <p class="text-xl font-semibold {{ $subscription->daysRemaining() <= 0 ? 'text-red-400' : 'text-white' }} mt-2">
                            {{ max(0, $subscription->daysRemaining()) }}
                        </p>
                    </div>
                </div>


                <div class="border-t border-white/8 pt-4">
                    <a href="{{ route('user.subscription.plans') }}" class="ud-btn-ghost">
                        @lang('View Plans')
                    </a>
                </div>
            </div>
        @else
            <div class="ud-empty">
                <x-heroicon-o-ticket class="h-12 w-12" />
                <div class="ud-empty-title">@lang('No active subscription')</div>
                <div class="ud-empty-body">@lang('Choose a plan to get started')</div>
                <a href="{{ route('user.subscription.plans') }}" class="ud-btn-primary mt-4 inline-flex">
                    @lang('View Plans')
                </a>
            </div>
        @endif
    </div>


    @if($subscription && $subscription->plan->hasCreditLimit())
    <div class="ud-card">
        <div class="ud-card-header">
            <h2 class="ud-card-title">@lang('Credit history')</h2>
        </div>

        @if($creditHistory->isEmpty())
            <div class="ud-empty">
                <x-heroicon-o-sparkles class="h-10 w-10" />
                <div class="ud-empty-title">@lang('No credit transactions yet')</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/8">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">@lang('Date')</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-white/45">@lang('Amount')</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white/45">@lang('Description')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($creditHistory as $log)
                        <tr class="hover:bg-white/3 transition-colors">
                            <td class="px-4 py-3 text-xs text-white/50 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right font-semibold whitespace-nowrap {{ $log->amount >= 0 ? 'text-lime' : 'text-red-400' }}">
                                {{ $log->amount >= 0 ? '+' : '' }}{{ $log->amount }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/70">{{ $log->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($creditHistory->hasPages())
            <div class="border-t border-white/8 mt-4 pt-4 text-xs text-white/45">
                {{ $creditHistory->links() }}
            </div>
            @endif
        @endif
    </div>
    @endif

</div>
@endsection

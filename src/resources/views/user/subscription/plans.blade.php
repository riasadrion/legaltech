@extends('theme::layouts.user')

@section('title', 'Plans & Pricing')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Plans & Pricing</h1>
        <p class="mt-1 text-gray-500">Choose the plan that works best for you</p>
    </div>

    {{-- Plans grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $plan)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col">
            {{-- Plan header --}}
            <div class="px-6 py-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h2>
                @if($plan->description)
                    <p class="mt-1 text-sm text-gray-600">{{ $plan->description }}</p>
                @endif
                <div class="mt-4">
                    <span class="text-3xl font-bold text-gray-900">{{ $plan->formattedPrice() }}</span>
                    <span class="text-gray-500 ml-2">/ {{ $plan->billing_cycle }}</span>
                </div>

                {{-- Trial badge --}}
                @if($plan->hasTrialPeriod())
                    <div class="mt-3 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ $plan->trial_days }}-day free trial
                    </div>
                @endif
            </div>

            {{-- Features --}}
            <div class="px-6 py-6 flex-1">
                @if($plan->features && count($plan->features) > 0)
                    <ul class="space-y-3">
                        @foreach($plan->features as $feature)
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No features listed</p>
                @endif
            </div>

            {{-- CTA Button --}}
            <div class="px-6 py-6 border-t border-gray-100">
                @if((float) $plan->price === 0.0 && $plan->hasTrialPeriod())
                    {{-- Free trial --}}
                    <form method="POST" action="{{ route('user.subscription.subscribe') }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                            Start free trial
                        </button>
                    </form>
                @else
                    {{-- Paid plan --}}
                    <form method="POST" action="{{ route('user.subscription.subscribe') }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                            Subscribe now
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-400">No plans available at the moment.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

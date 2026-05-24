@props([
    'plan',
    'featured'   => null,           // override; defaults to $plan->is_featured
    'action'     => null,           // ['type' => 'form'|'link', 'url' => '...', 'method' => 'POST']
    'ctaClass'   => null,           // optional override for CTA button classes
])

@php
    $featured     = $featured ?? (bool) $plan->is_featured;
    $isFree       = (float) $plan->price === 0.0;
    $isEnterprise = $isFree && \Illuminate\Support\Str::contains(strtolower($plan->cta_text ?? ''), 'sales');

    $priceText = $isEnterprise
        ? __('Custom')
        : ($isFree
            ? __('Free')
            : ($plan->price && fmod((float) $plan->price, 1) === 0.0
                ? setting('site.currency_symbol', '$') . number_format($plan->price, 0)
                : setting('site.currency_symbol', '$') . number_format($plan->price, 2)));

    $cycleSuffix = match ($plan->billing_cycle) {
        'monthly'      => __('/ month'),
        'yearly'       => __('/ year'),
        'per_contract' => __('/ contract'),
        default        => '',
    };

    $ctaText = $plan->cta_text ?: ($isEnterprise ? __('Talk to sales') : ($isFree ? __('Start free') : __('Subscribe now')));
    $ctaClassResolved = $ctaClass ?? ($featured ? 'btn-lime' : 'btn-ghost');
@endphp

<div class="{{ $featured ? 'relative rounded-3xl p-[1px] bg-gradient-to-b from-lime via-lime/40 to-transparent' : '' }} h-full">
    <div class="h-full flex flex-col {{ $featured ? 'rounded-[23px] bg-ink-900 p-7 relative overflow-hidden' : 'rounded-3xl glass p-7' }}">
        @if($featured)
            <div class="absolute -right-10 -top-10 w-56 h-56 glow-lime pointer-events-none"></div>
        @endif

        {{-- Header --}}
        <div class="relative flex items-center justify-between">
            <h3 class="font-display text-3xl text-white">{{ $plan->name }}</h3>
            @if($featured)
                <span class="text-[10px] font-semibold uppercase tracking-widest bg-lime text-ink-900 px-2.5 py-1 rounded-full">{{ __('Recommended') }}</span>
            @elseif($isEnterprise)
                <span class="text-[11px] font-mono text-white/45">{{ __('Custom') }}</span>
            @elseif($isFree)
                <span class="text-[11px] font-mono text-lime/80">{{ __('Free') }}</span>
            @endif
        </div>

        @if($plan->description)
            <p class="text-sm mt-1 relative {{ $featured ? 'text-white/65' : 'text-white/55' }}">{{ __($plan->description) }}</p>
        @endif

        {{-- Price --}}
        <div class="mt-6 flex items-baseline gap-1 relative">
            <span class="font-display text-5xl md:text-6xl text-white">{{ $priceText }}</span>
            @if(!$isFree && !$isEnterprise && $cycleSuffix)
                <span class="text-white/45">{{ $cycleSuffix }}</span>
            @endif
        </div>

        {{-- Features --}}
        <ul class="mt-6 space-y-2.5 text-sm relative flex-1 {{ $featured ? 'text-white/85' : 'text-white/75' }}">
            @if($plan->ai_contract_generation)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-sparkles class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                    <span class="text-white font-medium">
                        @if($plan->ai_drafts > 0)
                            {{ $plan->ai_drafts }} {{ trans_choice('AI draft|AI drafts', $plan->ai_drafts) }}
                        @else
                            {{ __('Unlimited AI drafts') }}
                        @endif
                    </span>
                </li>
            @endif

            <li class="flex items-start gap-2">
                <x-heroicon-s-check-circle class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                <span class="text-white">
                    @if($plan->credits > 0)
                        {{ $plan->credits }} {{ trans_choice('contract generation|contract generations', $plan->credits) }}
                    @else
                        {{ __('Unlimited contract generations') }}
                    @endif
                </span>
            </li>

            @if($plan->trial_days > 0)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-clock class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                    <span class="text-white">{{ $plan->trial_days }}-{{ __('day free trial') }}</span>
                </li>
            @endif

            @foreach(($plan->features ?? []) as $feature)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-check-circle class="h-4 w-4 text-lime mt-0.5 shrink-0" />
                    <span>{{ __($feature) }}</span>
                </li>
            @endforeach
        </ul>

        {{-- CTA --}}
        @if($action && ($action['type'] ?? 'link') === 'form')
            <form method="{{ $action['method'] ?? 'POST' }}" action="{{ $action['url'] }}" class="mt-7 relative">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <button type="submit" class="rounded-full px-5 py-3 text-sm w-full inline-flex items-center justify-center gap-2 {{ $ctaClassResolved }}">
                    {{ __($ctaText) }}
                    @if($featured)<x-heroicon-o-arrow-right class="h-4 w-4" />@endif
                </button>
            </form>
        @else
            <a href="{{ $action['url'] ?? ($plan->cta_url ?: '/register') }}"
               class="mt-7 rounded-full px-5 py-3 text-sm w-full inline-flex items-center justify-center gap-2 relative {{ $ctaClassResolved }}">
                {{ __($ctaText) }}
                @if($featured)<x-heroicon-o-arrow-right class="h-4 w-4" />@endif
            </a>
        @endif
    </div>
</div>

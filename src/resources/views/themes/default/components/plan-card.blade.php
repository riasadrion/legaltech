@props([
    'plan',
    'featured'   => null,           // override; defaults to $plan->is_featured
    'action'     => null,           // ['type' => 'link', 'url' => '...']
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
    $ctaClassResolved = $ctaClass ?? ($featured ? 'btn-primary text-on-primary-white' : 'border border-ink-900/15 text-ink-700 bg-white/50 hover:bg-white/80 backdrop-blur transition');

    $linkUrl = link_url($action['url'] ?? $plan->cta_url, '/register');
@endphp

<div class="{{ $featured ? 'relative rounded-3xl p-[1px] bg-gradient-to-b from-primary via-primary/40 to-transparent' : '' }} h-full">
    <div class="h-full flex flex-col {{ $featured ? 'rounded-[23px] bg-ink-900 p-7 relative overflow-hidden' : 'rounded-3xl glass-cream p-7' }}">
        @if($featured)
            <div class="absolute -right-10 -top-10 w-56 h-56 glow-primary pointer-events-none"></div>
        @endif


        <div class="relative flex items-center justify-between">
            <h3 class="font-display text-3xl {{ $featured ? 'text-white' : 'text-ink-950' }}">{{ $plan->name }}</h3>
            @if($featured)
                <span class="text-[10px] font-semibold uppercase tracking-widest bg-primary text-white px-2.5 py-1 rounded-full">@lang('Recommended')</span>
            @elseif($isEnterprise)
                <span class="text-[11px] font-mono text-ink-500">@lang('Custom')</span>
            @elseif($isFree)
                <span class="text-[11px] font-mono text-primary/80">@lang('Free')</span>
            @endif
        </div>

        @if($plan->description)
            <p class="text-sm mt-1 relative {{ $featured ? 'text-white/65' : 'text-ink-500' }}">{{ __($plan->description) }}</p>
        @endif


        <div class="mt-6 flex items-baseline gap-1 relative">
            <span class="font-display text-5xl md:text-6xl {{ $featured ? 'text-white' : 'text-ink-950' }}">{{ $priceText }}</span>
            @if(!$isFree && !$isEnterprise && $cycleSuffix)
                <span class="{{ $featured ? 'text-white/60' : 'text-ink-500' }}">{{ $cycleSuffix }}</span>
            @endif
        </div>


        <ul class="mt-6 space-y-2.5 text-sm relative flex-1 {{ $featured ? 'text-white/85' : 'text-ink-600' }}">
            @if($plan->ai_contract_generation)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-sparkles class="h-4 w-4 text-primary mt-0.5 shrink-0" />
                    <span class="{{ $featured ? 'text-white' : 'text-ink-950' }} font-medium">
                        @if($plan->ai_drafts > 0)
                            {{ $plan->ai_drafts }} {{ trans_choice('AI draft|AI drafts', $plan->ai_drafts) }}
                        @else
                            @lang('Unlimited AI drafts')
                        @endif
                    </span>
                </li>
            @endif

            <li class="flex items-start gap-2">
                <x-heroicon-s-check-circle class="h-4 w-4 text-primary mt-0.5 shrink-0" />
                <span class="{{ $featured ? 'text-white' : 'text-ink-950' }}">
                    @if($plan->credits > 0)
                        {{ $plan->credits }} {{ trans_choice('contract generation|contract generations', $plan->credits) }}
                    @else
                        @lang('Unlimited contract generations')
                    @endif
                </span>
            </li>

            @foreach(($plan->features ?? []) as $feature)
                <li class="flex items-start gap-2">
                    <x-heroicon-s-check-circle class="h-4 w-4 text-primary mt-0.5 shrink-0" />
                    <span>{{ __($feature) }}</span>
                </li>
            @endforeach
        </ul>


        <a href="{{ $linkUrl }}"
           class="mt-7 rounded-full px-5 py-3 text-sm w-full inline-flex items-center justify-center gap-2 relative {{ $ctaClassResolved }}">
            {{ __($ctaText) }}
            @if($featured)<x-heroicon-o-arrow-right class="h-4 w-4" />@endif
        </a>
    </div>
</div>

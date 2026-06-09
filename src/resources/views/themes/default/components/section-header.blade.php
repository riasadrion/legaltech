@props([
    'eyebrow' => null,
    'heading' => null,
    'subhead' => null,
    'align'   => 'left',
    'size'    => 'lg',
])

@php
    $alignClass = $align === 'center' ? 'text-center' : '';
    $headingSizeClass = $size === 'xl'
        ? 'text-5xl md:text-6xl'
        : 'text-4xl md:text-5xl lg:text-6xl';
@endphp

<div {{ $attributes->class([$alignClass]) }}>
    @if($eyebrow)
        <div class="text-[11px] font-mono uppercase tracking-[.25em] text-primary mb-3">— {{ __($eyebrow) }}</div>
    @endif
    @if($heading)
        <h2 class="font-display {{ $headingSizeClass }} leading-[0.95]"><?php echo __($heading); ?></h2>
    @endif
    @if($subhead)
        <p class="text-ink-600 mt-4">{{ __($subhead) }}</p>
    @endif
</div>

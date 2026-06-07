@props(['amount', 'currency' => null, 'locale' => null])

@php
    $currency = $currency ?? setting('site.currency', 'USD');
    $symbol = setting('site.currency_symbol', '$');
    $locale   = $locale ?? app()->getLocale();
    $value    = (float) $amount;

    try {
        $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $display = $fmt->formatCurrency($value, $currency);
    } catch (\Throwable) {
        // Fallback to settings symbol
        $display = $symbol . number_format($value, 2);
    }
@endphp

<span class="money" data-amount="{{ $value }}" data-currency="{{ $currency }}">{{ $display }}</span>

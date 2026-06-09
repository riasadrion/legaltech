@php
    $d = $section->getLocaleData();
    $body = (string) ($d['content'] ?? $d['body'] ?? '');
    $clean = \App\Services\HtmlPurifier::purify($body);
@endphp
@if($clean !== '')
<section class="relative py-28 {{ $sectionBg ?? 'bg-cream-50' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="blog-body html-body-card">
            <?php echo $clean; ?>
        </div>
    </div>
</section>
@endif

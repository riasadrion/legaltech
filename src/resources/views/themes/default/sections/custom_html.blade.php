@php
    $d = $section->getLocaleData();
    $body = (string) ($d['body'] ?? '');
    $clean = \App\Domain\Cms\Sanitizer\HtmlPurifier::purify($body);
@endphp
@if($clean !== '')
<section class="relative py-16">
    <div class="mx-auto max-w-4xl px-6 prose prose-invert">
        <?php echo $clean; ?>
    </div>
</section>
@endif

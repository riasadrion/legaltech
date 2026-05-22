@php
    $d = $section->getLocaleData();
    $body = (string) ($d['content'] ?? $d['body'] ?? '');
    $clean = \App\Domain\Cms\Sanitizer\HtmlPurifier::purify($body);
@endphp
@if($clean !== '')
<section class="relative py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="blog-body html-body-card">
            <?php echo $clean; ?>
        </div>
    </div>
</section>
@endif

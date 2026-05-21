@props(['content' => '', 'allowedTags' => null])
<?php echo app(\App\Domain\Cms\Sanitizer\HtmlPurifier::class)->clean($content ?? '', $allowedTags); ?>

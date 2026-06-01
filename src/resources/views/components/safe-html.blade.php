@props(['content' => '', 'allowedTags' => null])
<?php echo app(\App\Services\HtmlPurifier::class)->clean($content ?? '', $allowedTags); ?>

<?php

namespace App\Services;

class HtmlPurifier
{
    private static ?\HTMLPurifier $instance = null;

    public static function purify(?string $html): string
    {
        return (new self)->clean($html);
    }

    public function clean(?string $html, ?array $allowedTags = null): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $purifier = $allowedTags
            ? $this->makeWithTags($allowedTags)
            : $this->default();

        return $purifier->purify($html);
    }

    private function default(): \HTMLPurifier
    {
        if (self::$instance === null) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', storage_path('framework/cache/htmlpurifier'));
            $config->set('HTML.Allowed', implode(',', [
                'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'strong', 'b', 'em', 'i', 'u', 's',
                'ul', 'ol', 'li',
                'a[href|title|target|rel]',
                'img[src|alt|width|height]',
                'blockquote', 'pre', 'code',
                'table', 'thead', 'tbody', 'tr', 'th', 'td[colspan|rowspan]',
                'hr', 'span[class]', 'div[class]',
            ]));
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.TargetNoreferrer', true);
            $config->set('HTML.TargetNoopener', true);
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('AutoFormat.Linkify', false);

            self::$instance = new \HTMLPurifier($config);
        }

        return self::$instance;
    }

    private function makeWithTags(array $allowedTags): \HTMLPurifier
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage_path('framework/cache/htmlpurifier'));
        $config->set('HTML.Allowed', implode(',', $allowedTags));

        return new \HTMLPurifier($config);
    }
}

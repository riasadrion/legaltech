<?php

namespace App\Support;

class ViteAssets
{
    private static ?array $manifest = null;

    public static function getManifest(): array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $path = base_path('../assets/vite/manifest.json');

        if (!file_exists($path)) {
            return self::$manifest = [];
        }

        return self::$manifest = json_decode(file_get_contents($path), true) ?? [];
    }

    public static function asset(string $entry): string
    {
        $manifest = self::getManifest();

        if (!isset($manifest[$entry])) {
            return '';
        }

        $file = $manifest[$entry]['file'];
        $basePath = rtrim(request()->getBasePath(), '/') . '/';

        return $basePath . 'assets/vite/' . $file;
    }

    public static function includeAssets(array $entries): string
    {
        $manifest  = self::getManifest();
        $html      = '';
        $basePath  = rtrim(request()->getBasePath(), '/') . '/';

        foreach ($entries as $entry) {
            if (!isset($manifest[$entry])) {
                continue;
            }

            $chunk     = $manifest[$entry];
            $assetPath = $basePath . 'assets/vite/' . $chunk['file'];

            // CSS chunks emitted from JS entries
            foreach ($chunk['css'] ?? [] as $cssFile) {
                $html .= '<link rel="stylesheet" href="' . $basePath . 'assets/vite/' . $cssFile . '">' . "\n";
            }

            if (str_ends_with($assetPath, '.css')) {
                $html .= '<link rel="stylesheet" href="' . $assetPath . '">' . "\n";
            } elseif (str_ends_with($assetPath, '.js')) {
                $html .= '<script src="' . $assetPath . '" type="module"></script>' . "\n";
            }
        }

        return $html;
    }
}

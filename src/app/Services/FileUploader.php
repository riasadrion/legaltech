<?php

namespace App\Services;

use App\Facades\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    public function store(
        UploadedFile $file,
        string $module,
        ?int $ownerId = null,
        ?int $subResourceId = null,
        ?int $maxWidth = null,
        ?string $prefix = null,
    ): UploadedFileMeta {
        $config = $this->moduleConfig($module);

        $this->validateExtension($file, $config);
        $this->validateSize($file, $config);

        $realMime = $this->sniffMime($file);
        $this->assertMimeAllowed($realMime, $config, $module);

        $filename = $this->buildFilename($file, $realMime, $prefix);
        $directory = $this->resolveDirectory($config, $module, $ownerId, $subResourceId);
        $path = rtrim($directory, '/').'/'.$filename;

        $disk = $this->resolveDisk($config);
        $this->writeFile($file, $realMime, $path, $disk, $maxWidth ?? $config['max_width'] ?? null);

        $publicUrl = $config['public'] ? $path : null;

        return new UploadedFileMeta(
            path: $path,
            disk: $disk,
            originalName: $file->getClientOriginalName(),
            mime: $realMime,
            size: $file->getSize(),
            url: $publicUrl,
        );
    }

    private function moduleConfig(string $module): array
    {
        $modules = config('uploads.modules', []);
        if (! isset($modules[$module])) {
            throw new \InvalidArgumentException("Unknown upload module [{$module}].");
        }

        return $modules[$module];
    }

    private function validateExtension(UploadedFile $file, array $config): void
    {
        $ext = strtolower($file->getClientOriginalExtension());

        $originalName = $file->getClientOriginalName();
        $parts = explode('.', $originalName);
        if (count($parts) > 2) {
            $penultimate = strtolower($parts[count($parts) - 2]);
            $phpExts = ['php', 'phtml', 'phar', 'html', 'htm', 'js', 'sh', 'py', 'rb', 'pl'];
            if (in_array($penultimate, $phpExts, true)) {
                throw new \InvalidArgumentException('Double extension detected in filename.');
            }
        }

        $extMap = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'webp' => 'image/webp',
            'gif' => 'image/gif', 'pdf' => 'application/pdf',
            'txt' => 'text/plain',
        ];

        $extMime = $extMap[$ext] ?? null;
        if (! $extMime || ! in_array($extMime, $config['mime'], true)) {
            throw new \InvalidArgumentException("File extension .{$ext} is not allowed.");
        }
    }

    private function validateSize(UploadedFile $file, array $config): void
    {
        $maxBytes = ($config['max_kb'] ?? 2048) * 1024;
        if ($file->getSize() > $maxBytes) {
            $mb = round($maxBytes / 1024 / 1024, 1);
            throw new \InvalidArgumentException("File exceeds maximum size of {$mb} MB.");
        }
    }

    private function sniffMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->file($file->getRealPath());
    }

    private function assertMimeAllowed(string $mime, array $config, string $module): void
    {
        if (! in_array($mime, $config['mime'], true)) {
            throw new \InvalidArgumentException("File type [{$mime}] is not allowed for module [{$module}].");
        }
    }

    private function resolveDirectory(array $config, string $module, ?int $ownerId, ?int $subResourceId): string
    {
        return match ($config['pattern']) {
            'module' => $module,
            'module-owner' => $module.'/'.($ownerId ?? 0),
            'module-yearmonth' => $module.'/'.now()->format('Y/m'),
            'module-owner-submission' => $module.'/'.($ownerId ?? 0).'/'.($subResourceId ?? now()->format('Ym')),
            default => $module,
        };
    }

    private function resolveDisk(array $config): string
    {
        if (! $config['public']) {
            return 'private';
        }

        try {
            $driver = Settings::string('storage.driver', 'local');

            return $driver === 'local' ? 'uploads' : $driver;
        } catch (\Throwable) {
            return 'uploads';
        }
    }

    private function buildFilename(UploadedFile $file, string $mime, ?string $prefix = null): string
    {
        if ($prefix !== null) {
            $slug = Str::slug($prefix);
        } else {
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = Str::slug($original);
            if ($slug === '') {
                $slug = 'file';
            }
        }
        $suffix = Str::random(8);
        $ext = $this->extensionForMime($mime);

        return "{$slug}-{$suffix}.{$ext}";
    }

    private function writeFile(UploadedFile $file, string $mime, string $path, string $disk, ?int $maxWidth = null): void
    {
        if (str_starts_with($mime, 'image/') && $mime !== 'image/gif') {
            $reencoded = $this->reencodeImage($file->getRealPath(), $mime, $maxWidth);
            if ($reencoded !== null) {
                Storage::disk($disk)->put($path, $reencoded);

                return;
            }
        }

        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));
    }

    private function reencodeImage(string $realPath, string $mime, ?int $maxWidth = null): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $img = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($realPath),
            'image/png' => @imagecreatefrompng($realPath),
            'image/webp' => @imagecreatefromwebp($realPath),
            default => null,
        };

        if (! $img) {
            return null;
        }

        if ($maxWidth !== null && imagesx($img) > $maxWidth) {
            $origW = imagesx($img);
            $origH = imagesy($img);
            $newW = $maxWidth;
            $newH = (int) round($origH * $maxWidth / $origW);
            $resized = imagecreatetruecolor($newW, $newH);
            if ($mime === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($img);
            $img = $resized;
        }

        ob_start();
        match ($mime) {
            'image/jpeg' => imagejpeg($img, null, 90),
            'image/png' => imagepng($img),
            'image/webp' => imagewebp($img, null, 90),
            default => null,
        };
        $output = ob_get_clean();
        imagedestroy($img);

        return $output ?: null;
    }

    private function extensionForMime(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            default => 'bin',
        };
    }
}

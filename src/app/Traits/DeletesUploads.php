<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait DeletesUploads
{
    protected function deleteUpload(?string $path): void
    {
        if (! $path) {
            return;
        }

        $needle = '/assets/uploads/';
        $pos    = strpos($path, $needle);

        Storage::disk('uploads')->delete(
            $pos !== false ? substr($path, $pos + strlen($needle)) : ltrim($path, '/')
        );
    }
}

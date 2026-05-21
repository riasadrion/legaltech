<?php

namespace App\Domain\Upload;

class UploadedFileMeta
{
    public function __construct(
        public readonly string $path,
        public readonly string $disk,
        public readonly string $originalName,
        public readonly string $mime,
        public readonly int $size,
        public readonly ?string $url = null,
    ) {}

    public function isPublic(): bool
    {
        return $this->url !== null;
    }

    public function formattedSize(): string
    {
        if ($this->size < 1024) {
            return $this->size.' B';
        }
        if ($this->size < 1024 * 1024) {
            return round($this->size / 1024, 1).' KB';
        }

        return round($this->size / 1024 / 1024, 1).' MB';
    }
}

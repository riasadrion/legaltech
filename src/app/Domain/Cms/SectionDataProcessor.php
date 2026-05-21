<?php

namespace App\Domain\Cms;

use App\Domain\Upload\FileUploader;
use Illuminate\Support\Facades\Storage;

/**
 * Walks a section's field schema and:
 *   - Reads scalars/repeaters from form input
 *   - Uploads files for image fields
 *   - Cleans up orphaned uploads (rows removed by admin)
 */
class SectionDataProcessor
{
    public function __construct(private FileUploader $uploader) {}

    /**
     * @param  array  $schema   The 'fields' array from config/sections.php
     * @param  array  $input    request()->input('data.en', []) — scalar/array values
     * @param  array  $files    request()->file('data.en', []) — uploaded file tree
     * @param  array  $previous The previously saved data for this locale (for orphan cleanup)
     * @return array  The cleaned, persisted-ready data
     */
    public function process(array $schema, array $input, array $files, array $previous): array
    {
        $oldImageUrls = $this->collectImageUrls($schema, $previous);
        $output = $this->walk($schema, $input, $files);
        $newImageUrls = $this->collectImageUrls($schema, $output);

        // Delete files for image fields that were removed or replaced
        foreach ($oldImageUrls as $oldUrl) {
            if (! in_array($oldUrl, $newImageUrls, true)) {
                $this->deleteUpload($oldUrl);
            }
        }

        return $output;
    }

    private function walk(array $schema, array $input, array $files): array
    {
        $output = [];
        foreach ($schema as $field) {
            $type = $field['type'];
            $key = $field['key'];

            switch ($type) {
                case 'text':
                case 'url':
                case 'textarea':
                case 'richtext':
                case 'select':
                    $output[$key] = (string) ($input[$key] ?? '');
                    break;

                case 'checkbox':
                    $output[$key] = (bool) ($input[$key] ?? false);
                    break;

                case 'image':
                    $output[$key] = $this->resolveImage(
                        existingUrl: (string) ($input[$key] ?? ''),
                        file: $files[$key.'_file'] ?? null,
                        module: $field['module'] ?? 'sections',
                    );
                    break;

                case 'group':
                    $output[$key] = $this->walk(
                        $field['fields'],
                        $input[$key] ?? [],
                        $files[$key] ?? [],
                    );
                    break;

                case 'repeater':
                    $items = $input[$key] ?? [];
                    $itemFiles = $files[$key] ?? [];
                    $output[$key] = [];
                    foreach ($items as $i => $itemInput) {
                        if (! is_array($itemInput)) {
                            continue;
                        }
                        $output[$key][] = $this->walk(
                            $field['item_fields'],
                            $itemInput,
                            $itemFiles[$i] ?? [],
                        );
                    }
                    $output[$key] = array_values(array_filter(
                        $output[$key],
                        fn ($item) => $this->repeaterItemIsMeaningful($item),
                    ));
                    break;
            }
        }

        return $output;
    }

    private function resolveImage(string $existingUrl, ?\Illuminate\Http\UploadedFile $file, string $module): string
    {
        if ($file) {
            $meta = $this->uploader->store($file, $module);

            return $meta->url ?? '';
        }

        return $existingUrl;
    }

    private function repeaterItemIsMeaningful(array $item): bool
    {
        foreach ($item as $v) {
            if (is_array($v)) {
                if ($this->repeaterItemIsMeaningful($v)) {
                    return true;
                }
            } elseif ($v !== '' && $v !== false && $v !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Walk a schema + data tree and return every URL stored in an `image` field.
     */
    private function collectImageUrls(array $schema, array $data): array
    {
        $urls = [];
        foreach ($schema as $field) {
            $key = $field['key'];
            $value = $data[$key] ?? null;

            switch ($field['type']) {
                case 'image':
                    if (is_string($value) && $value !== '') {
                        $urls[] = $value;
                    }
                    break;
                case 'group':
                    if (is_array($value)) {
                        $urls = array_merge($urls, $this->collectImageUrls($field['fields'], $value));
                    }
                    break;
                case 'repeater':
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            if (is_array($item)) {
                                $urls = array_merge($urls, $this->collectImageUrls($field['item_fields'], $item));
                            }
                        }
                    }
                    break;
            }
        }

        return $urls;
    }

    private function deleteUpload(string $path): void
    {
        if ($path === '') {
            return;
        }
        // Stored value is a relative path (e.g. "sections/file.png").
        // Legacy values may still be full URLs — strip the prefix if present.
        $needle = '/assets/uploads/';
        if (($pos = strpos($path, $needle)) !== false) {
            $path = substr($path, $pos + strlen($needle));
        }
        Storage::disk('uploads')->delete($path);
    }
}

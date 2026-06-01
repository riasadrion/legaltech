<?php

namespace App\Services;

/**
 * Normalizes the compact positional schema syntax used in config/sections.php
 * into the named-array shape consumed by the renderer and processor.
 *
 * Accepted shapes:
 *   ['key', 'type', 'label']                     — a simple field
 *   ['key', 'label', [...itemFieldsArray]]       — a repeater (type inferred)
 *
 * An entry that already uses named keys (['key' => ..., 'type' => ...]) is left untouched.
 */
class SchemaNormalizer
{
    public static function normalize(array $schema): array
    {
        $out = [];
        foreach ($schema as $entry) {
            // Already normalized?
            if (isset($entry['key']) || isset($entry['type'])) {
                if (! empty($entry['item_fields'])) {
                    $entry['item_fields'] = self::normalize($entry['item_fields']);
                }

                $out[] = $entry;

                continue;
            }

            // Positional. Slot 0 = key. If slot 2 is an array → repeater.
            $key = $entry[0] ?? null;
            $second = $entry[1] ?? null;
            $third = $entry[2] ?? null;

            if (is_array($third)) {
                $out[] = [
                    'key' => $key,
                    'type' => 'repeater',
                    'label' => $second,
                    'item_fields' => self::normalize($third),
                ];
            } else {
                $out[] = [
                    'key' => $key,
                    'type' => $second,
                    'label' => $third,
                ];
            }
        }

        return $out;
    }
}

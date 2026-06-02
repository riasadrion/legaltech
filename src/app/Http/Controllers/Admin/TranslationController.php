<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use App\Models\PageSection;
use App\Http\Controllers\Controller;
use App\Support\GoogleTranslate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::orderBy('sort')->get();
        $defaultLocale = Language::default()?->code ?? 'en';
        $locale = $request->get('locale', $defaultLocale);
        $selectedLang = Language::findByCode($locale);
        $langDirection = $selectedLang?->direction ?? 'ltr';

        $allTranslations = $this->readJsonTranslations($defaultLocale);

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $allTranslations = array_filter($allTranslations, function ($key, $value) use ($q) {
                return str_contains(strtolower($key), $q) || str_contains(strtolower($value), $q);
            }, ARRAY_FILTER_USE_BOTH);
        }

        ksort($allTranslations);

        $allObjects = collect(array_keys($allTranslations))
            ->map(fn ($key) => (object) ['key' => $key]);

        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allObjects->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $translations = new LengthAwarePaginator(
            $currentPageItems,
            $allObjects->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $localeTranslations = $this->readJsonTranslations($locale);
        $existingTranslations = [];
        foreach ($currentPageItems as $obj) {
            if (isset($localeTranslations[$obj->key]) && $localeTranslations[$obj->key] !== $obj->key) {
                $existingTranslations[$obj->key] = $localeTranslations[$obj->key];
            }
        }

        return view('admin.translations.index', compact(
            'languages', 'defaultLocale', 'locale', 'translations',
            'existingTranslations', 'langDirection', 'selectedLang'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', 'size:2'],
            'value' => ['nullable', 'string'],
        ]);

        $defaultLocale = Language::default()?->code ?? 'en';

        $defaultTranslations = $this->readJsonTranslations($defaultLocale);
        $defaultTranslations[$data['key']] = $data['key'];
        $this->writeJsonTranslations($defaultLocale, $defaultTranslations);

        if ($data['locale'] !== $defaultLocale) {
            $localeTranslations = $this->readJsonTranslations($data['locale']);
            $localeTranslations[$data['key']] = $data['value'] ?? '';
            $this->writeJsonTranslations($data['locale'], $localeTranslations);
        }

        return back()->with('success', 'Translation added.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'translations' => ['required', 'array'],
            'translations.*.locale' => ['required', 'string', 'size:2'],
            'translations.*.key' => ['required', 'string'],
            'translations.*.value' => ['nullable', 'string'],
        ]);

        $grouped = collect($request->translations)->groupBy('locale');

        foreach ($grouped as $locale => $items) {
            $translations = $this->readJsonTranslations($locale);
            foreach ($items as $item) {
                $translations[$item['key']] = $item['value'] ?? '';
            }
            $this->writeJsonTranslations($locale, $translations);
        }

        return back()->with('success', 'Translations saved.');
    }

    public function autoTranslate(Request $request)
    {
        $data = $request->validate([
            'locale' => ['required', 'string', 'size:2'],
        ]);

        $defaultLocale = Language::default()?->code ?? 'en';
        $locale = $data['locale'];

        if ($locale === $defaultLocale) {
            return response()->json(['error' => 'Cannot translate the default language.'], 400);
        }

        $sourceTranslations = $this->readJsonTranslations($defaultLocale);
        $targetTranslations = $this->readJsonTranslations($locale);

        $pendingKeys = [];
        foreach ($sourceTranslations as $key => $value) {
            $targetValue = $targetTranslations[$key] ?? '';
            if (trim($targetValue) === '') {
                $pendingKeys[] = ['key' => $key, 'value' => $value];
            }
        }

        \Log::info('AutoTranslate fetch', [
            'locale' => $locale,
            'total_pending' => count($pendingKeys),
            'source_total' => count($sourceTranslations),
            'target_total' => count($targetTranslations),
            'missing_keys' => count($pendingKeys),
        ]);

        return response()->json([
            'total' => count($pendingKeys),
            'pending' => $pendingKeys,
        ]);
    }

    public function syncKeywords(Request $request)
    {
        try {
            $defaultLocale = Language::default()?->code ?? 'en';
            $languages = Language::pluck('code')->toArray();

            $scanned = array_unique(array_merge(
                $this->scanTranslationKeys(),
                $this->scanCmsKeys(),
            ));

            $defaultTranslations = $this->readJsonTranslations($defaultLocale);
            $newKeys = [];
            foreach ($scanned as $key) {
                if (! array_key_exists($key, $defaultTranslations)) {
                    $defaultTranslations[$key] = $key;
                    $newKeys[] = $key;
                }
            }
            $this->writeJsonTranslations($defaultLocale, $defaultTranslations);

            foreach ($languages as $locale) {
                if ($locale === $defaultLocale) continue;

                $localeTranslations = $this->readJsonTranslations($locale);
                foreach ($newKeys as $key) {
                    if (! array_key_exists($key, $localeTranslations)) {
                        $localeTranslations[$key] = '';
                    }
                }
                $this->writeJsonTranslations($locale, $localeTranslations);
            }

            return response()->json([
                'found'   => count($defaultTranslations),
                'scanned' => count($scanned),
                'added'   => count($newKeys),
                'new_keys' => $newKeys,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Scan all Blade and PHP files for translatable text.
     * Only detects: __('key'), trans('key'), @lang('key')
     */
    private function scanTranslationKeys(): array
    {
        $keys = [];
        $paths = [
            resource_path('views'),
            resource_path('themes'),
            app_path(),
        ];

        foreach ($paths as $path) {
            if (! is_dir($path)) continue;

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! in_array($file->getExtension(), ['php'])) continue;
                if (str_contains($file->getPathname(), 'vendor')) continue;

                $content = file_get_contents($file->getPathname());

                preg_match_all(
                    '/(?:__|trans|trans_choice|@lang)\s*\(\s*[\'"]([a-zA-Z][a-zA-Z0-9\s\:\-\_\.\,\!\?\/\—\'\"]{1,199})[\'"]\s*\)/',
                    $content,
                    $matches
                );
                foreach ($matches[1] ?? [] as $k) {
                    $k = trim($k);
                    if (preg_match('/^[a-zA-Z][a-zA-Z0-9\s\:\-\_\.\,\!\?\/\—\'\"]+$/', $k)
                        && ! str_contains($k, '$')
                        && ! str_contains($k, '->')
                        && ! str_contains($k, '(')
                        && ! str_contains($k, ')')
                        && ! str_contains($k, '+')
                        && ! str_contains($k, '//')
                        && strlen($k) >= 2 && strlen($k) <= 200) {
                        $keys[$k] = true;
                    }
                }
            }
        }

        return array_keys($keys);
    }

    /**
     * Extract human-readable strings from all CMS page sections (page_sections.data).
     * These are output via __() in section Blade templates but are dynamic DB values,
     * so the file scanner cannot detect them.
     */
    private function scanCmsKeys(): array
    {
        $keys = [];

        PageSection::all()->each(function (PageSection $section) use (&$keys) {
            $this->extractStringsFromValue($section->data ?? [], $keys);
        });

        return array_keys($keys);
    }

    private function extractStringsFromValue(mixed $value, array &$keys): void
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->extractStringsFromValue($v, $keys);
            }
            return;
        }

        if (! is_string($value)) return;

        $v = trim($value);

        if ($v === '' || strlen($v) < 2 || strlen($v) > 300) return;
        if (preg_match('/^https?:\/\//', $v)) return;         // absolute URLs
        if (preg_match('/^[\/\#]/', $v)) return;              // paths and anchors
        if (is_numeric($v)) return;
        if (str_contains($v, '<') && str_contains($v, '>')) return; // HTML
        if (preg_match('/^ri-[a-z]/', $v)) return;            // Remix icon class names
        if (preg_match('/\.(png|jpg|jpeg|webp|svg|gif)$/i', $v)) return; // image filenames

        $keys[$v] = true;
    }

    public function autoTranslateBatch(Request $request)
    {
        set_time_limit(120);

        $data = $request->validate([
            'locale' => ['required', 'string', 'size:2'],
            'batch' => ['required', 'array', 'max:50'],
            'batch.*.key' => ['required', 'string'],
            'batch.*.value' => ['required', 'string'],
        ]);

        $defaultLocale = Language::default()?->code ?? 'en';
        $locale = $data['locale'];
        $lockFile = storage_path("locks/translations-{$locale}.lock");

        File::ensureDirectoryExists(dirname($lockFile));

        $lockHandle = fopen($lockFile, 'c');
        if (!flock($lockHandle, LOCK_EX)) {
            fclose($lockHandle);
            return response()->json(['error' => 'Could not acquire lock'], 500);
        }

        try {
            $translations = $this->readJsonTranslations($locale);
            $translated = 0;
            $batchKeys = [];

            foreach ($data['batch'] as $item) {
                $result = GoogleTranslate::translate($item['value'], $defaultLocale, $locale);
                $translations[$item['key']] = $result;
                $batchKeys[] = $item['key'];
                $translated++;
                usleep(300000);
            }

            $this->writeJsonTranslations($locale, $translations);

            \Log::info('AutoTranslateBatch completed', [
                'locale' => $locale,
                'translated_count' => $translated,
                'batch_keys' => $batchKeys,
                'total_translations_now' => count($translations),
            ]);

            return response()->json(['translated' => $translated]);
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    public function destroy(string $key)
    {
        $languages = Language::pluck('code');
        foreach ($languages as $locale) {
            $translations = $this->readJsonTranslations($locale);
            unset($translations[$key]);
            $this->writeJsonTranslations($locale, $translations);
        }

        return back()->with('success', "Key '{$key}' removed from all languages.");
    }

    private function readJsonTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }

    private function writeJsonTranslations(string $locale, array $translations): void
    {
        File::ensureDirectoryExists(lang_path());
        $path = lang_path("{$locale}.json");
        $json = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        \Log::debug('Writing translations', [
            'locale' => $locale,
            'path' => $path,
            'key_count' => count($translations),
            'json_length' => strlen($json),
            'path_exists_before' => File::exists($path),
        ]);

        File::put($path, $json);

        $verifyCount = count(json_decode(File::get($path), true) ?? []);
        \Log::debug('Translations written and verified', [
            'locale' => $locale,
            'keys_written' => count($translations),
            'keys_verified' => $verifyCount,
        ]);
    }
}

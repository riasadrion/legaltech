<?php

namespace App\Http\Controllers\Admin;

use App\Domain\I18n\Models\Language;
use App\Http\Controllers\Controller;
use App\Support\GoogleTranslate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('sort')->orderBy('name')->get();

        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $t0 = microtime(true);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'native_name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'size:2', 'unique:languages,code'],
            'direction' => ['required', 'in:ltr,rtl'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $language = new Language;
        $language->name = $data['name'];
        $language->native_name = $data['native_name'];
        $language->code = strtolower($data['code']);
        $language->direction = $data['direction'];
        $language->sort = $data['sort'] ?? 0;
        $language->is_active = $request->boolean('is_active', true);
        $language->is_default = false;
        $language->save();

        $t1 = microtime(true);

        $defaultLocale = Language::default()?->code ?? 'en';

        $scanned = $this->scanTranslationKeys();

        $t2 = microtime(true);

        $defaultTranslations = $this->readJsonTranslations($defaultLocale);

        foreach ($scanned as $key) {
            if (! array_key_exists($key, $defaultTranslations)) {
                $defaultTranslations[$key] = $key;
            }
        }
        $this->writeJsonTranslations($defaultLocale, $defaultTranslations);

        $t3 = microtime(true);

        $newTranslations = [];
        foreach ($defaultTranslations as $key => $value) {
            $newTranslations[$key] = '';
        }
        $this->writeJsonTranslations($language->code, $newTranslations);

        $t4 = microtime(true);

        \Log::info('Language creation timing', [
            'validate' => round(($t1 - $t0) * 1000, 2) . 'ms',
            'save_db' => round(($t1 - $t0) * 1000, 2) . 'ms',
            'scan' => round(($t2 - $t1) * 1000, 2) . 'ms',
            'read_write_en' => round(($t3 - $t2) * 1000, 2) . 'ms',
            'write_new' => round(($t4 - $t3) * 1000, 2) . 'ms',
            'total' => round(($t4 - $t0) * 1000, 2) . 'ms',
        ]);

        $count = count($defaultTranslations);

        return redirect()->route('admin.languages.index')->with('success', "Language '{$language->name}' created with {$count} keys. Use auto-translate to fill in translations.");
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'native_name' => ['required', 'string', 'max:100'],
            'direction' => ['required', 'in:ltr,rtl'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $language->name = $data['name'];
        $language->native_name = $data['native_name'];
        $language->direction = $data['direction'];
        $language->sort = $data['sort'] ?? 0;
        $language->is_active = $request->boolean('is_active', true);
        $language->save();

        return redirect()->route('admin.languages.index')->with('success', 'Language updated.');
    }

    public function setDefault(Language $language)
    {
        Language::where('is_default', true)->update(['is_default' => false]);
        $language->is_default = true;
        $language->is_active = true;
        $language->save();

        return back()->with('success', "{$language->name} set as default language.");
    }

    public function toggle(Language $language)
    {
        if ($language->is_default) {
            return back()->withErrors(['error' => 'Cannot disable the default language.']);
        }

        $language->is_active = ! $language->is_active;
        $language->save();

        return back()->with('success', $language->is_active ? 'Language enabled.' : 'Language disabled.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->withErrors(['error' => 'Cannot delete the default language.']);
        }

        $path = lang_path("{$language->code}.json");
        if (File::exists($path)) {
            File::delete($path);
        }

        $language->delete();

        return back()->with('success', 'Language deleted.');
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
        File::put(
            lang_path("{$locale}.json"),
            json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
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
}

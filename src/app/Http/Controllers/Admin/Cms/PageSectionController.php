<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Models\Page;
use App\Models\PageSection;
use App\Services\SchemaNormalizer;
use App\Services\SectionDataProcessor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    public function store(Request $request, Page $page)
    {
        $request->validate([
            'source' => ['required', 'string'],
        ]);

        $source = $request->input('source');

        // "existing:{id}" → attach an existing section to this page
        if (str_starts_with($source, 'existing:')) {
            $id = (int) substr($source, 9);
            $section = PageSection::find($id);

            if (! $section) {
                return back()->with('error', 'Section not found.');
            }
            if (in_array($section->id, $page->section_ids ?? [])) {
                return back()->with('error', 'Section is already on this page.');
            }

            $config = config('sections.'.$section->type);
            if ($config && $config['max_per_page'] !== null) {
                $count = $page->sections()->filter(fn ($s) => $s->type === $section->type)->count();
                if ($count >= $config['max_per_page']) {
                    return back()->with('error', 'This section type is already at its limit for this page.');
                }
            }

            $page->addSection($section);

            return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Existing section attached.');
        }

        // Otherwise the source IS the type — create a new section
        $type = $source;
        $config = config('sections.'.$type);

        if (! $config) {
            return back()->with('error', 'Unknown section type.');
        }

        // Only custom_html can have multiple instances. All other types are singletons —
        // if one already exists anywhere in the system, the admin must reuse it.
        if ($type !== 'custom_html' && PageSection::where('type', $type)->exists()) {
            return back()->with('error', 'This section type already exists. Use "Reuse existing" to attach it.');
        }

        $section = new PageSection;
        $section->type = $type;
        $section->data = [];
        $section->enabled = true;
        $section->sort = count($page->section_ids ?? []);
        $section->save();

        $page->addSection($section);

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Section created and added.');
    }

    public function edit(Page $page, PageSection $section)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);
        abort_if(! config('sections.'.$section->type), 404);

        return view('admin.cms.sections.edit', compact('page', 'section'));
    }

    public function update(Request $request, Page $page, PageSection $section, SectionDataProcessor $processor)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);

        $config = config('sections.'.$section->type);
        abort_if(! $config, 404);

        $request->validate(['enabled' => ['boolean']]);

        $data = (array) $request->input('data', []);
        $files = (array) $request->file('data', []);

        if (! empty($config['fields'])) {
            $schema = SchemaNormalizer::normalize($config['fields']);
            $data = $processor->process(
                schema: $schema,
                input: $data,
                files: $files,
                previous: (array) ($section->data ?? []),
            );
        }

        $section->data = $data;
        $section->enabled = $request->boolean('enabled', true);
        $section->save();

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Section updated.');
    }

    public function detach(Page $page, PageSection $section)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);

        $page->removeSection($section->id);

        return back()->with('success', 'Section detached from page.');
    }

    public function destroy(Page $page, PageSection $section)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);

        // Only ad-hoc content (custom_html) can be fully deleted.
        if ($section->type !== 'custom_html') {
            return back()->with('error', 'Only custom HTML sections can be deleted. Detach instead.');
        }

        $page->removeSection($section->id);
        $section->delete();

        return back()->with('success', 'Section deleted.');
    }

    public function reorder(Request $request, Page $page)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['numeric']]);

        $page->reorderSections(array_map('intval', $request->input('order')));

        return response()->json(['ok' => true]);
    }

    public function toggle(Page $page, PageSection $section)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);

        $section->enabled = ! $section->enabled;
        $section->save();

        return back()->with('success', $section->enabled ? 'Section enabled.' : 'Section disabled.');
    }
}

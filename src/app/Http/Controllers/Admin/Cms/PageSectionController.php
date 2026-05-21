<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Domain\Cms\Models\Page;
use App\Domain\Cms\Models\PageSection;
use App\Domain\Cms\SchemaNormalizer;
use App\Domain\Cms\SectionDataProcessor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    public function store(Request $request, Page $page)
    {
        $request->validate(['type' => ['required', 'string']]);
        $type = $request->input('type');
        $config = config('sections.'.$type);

        if (! $config) {
            return back()->with('error', 'Unknown section type.');
        }

        if ($config['max_per_page'] !== null) {
            $count = $page->sections()->filter(fn ($s) => $s->type === $type)->count();
            if ($count >= $config['max_per_page']) {
                return back()->with('error', 'This section type is already at its limit for this page.');
            }
        }

        $section = new PageSection;
        $section->type = $type;
        $section->data = [];
        $section->enabled = true;
        $section->sort = count($page->section_ids ?? []);
        $section->save();

        $page->addSection($section);

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Section added.');
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

    public function destroy(Page $page, PageSection $section)
    {
        abort_unless(in_array($section->id, $page->section_ids ?? []), 404);

        $config = config('sections.'.$section->type);
        if ($config && ! $config['deletable']) {
            return back()->with('error', 'This section cannot be deleted.');
        }

        $page->removeSection($section->id);
        $section->delete();

        return back()->with('success', 'Section removed.');
    }

    public function reorder(Request $request, Page $page)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

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

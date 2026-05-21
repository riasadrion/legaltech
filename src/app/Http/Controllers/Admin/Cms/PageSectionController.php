<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Domain\Cms\Models\Page;
use App\Domain\Cms\Models\PageSection;
use App\Domain\I18n\Models\Language;
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
            $count = $page->sections()->where('type', $type)->count();
            if ($count >= $config['max_per_page']) {
                return back()->with('error', 'This section type is already at its limit for this page.');
            }
        }

        $maxSort = $page->sections()->max('sort') ?? -1;

        $section = new PageSection;
        $section->page_id = $page->id;
        $section->type = $type;
        $section->data = $config['default_data'];
        $section->enabled = true;
        $section->sort = $maxSort + 1;
        $section->save();

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Section added.');
    }

    public function edit(Page $page, PageSection $section)
    {
        abort_if($section->page_id !== $page->id, 404);
        abort_if(! config('sections.'.$section->type), 404);

        $locales = Language::orderBy('is_default', 'desc')->get();

        return view('admin.cms.sections.edit', compact('page', 'section', 'locales'));
    }

    public function update(Request $request, Page $page, PageSection $section)
    {
        abort_if($section->page_id !== $page->id, 404);

        $config = config('sections.'.$section->type);
        abort_if(! $config, 404);

        $request->validate(['enabled' => ['boolean']]);

        $data = $request->input('data', []);

        $section->data = $data;
        $section->enabled = $request->boolean('enabled', true);
        $section->save();

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Section updated.');
    }

    public function destroy(Page $page, PageSection $section)
    {
        abort_if($section->page_id !== $page->id, 404);

        $config = config('sections.'.$section->type);
        if ($config && ! $config['deletable']) {
            return back()->with('error', 'This section cannot be deleted.');
        }

        $section->delete();

        return back()->with('success', 'Section removed.');
    }

    public function reorder(Request $request, Page $page)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->input('order') as $sort => $id) {
            $page->sections()->where('id', $id)->update(['sort' => $sort]);
        }

        return response()->json(['ok' => true]);
    }

    public function toggle(Page $page, PageSection $section)
    {
        abort_if($section->page_id !== $page->id, 404);

        $section->enabled = ! $section->enabled;
        $section->save();

        return back()->with('success', $section->enabled ? 'Section enabled.' : 'Section disabled.');
    }
}

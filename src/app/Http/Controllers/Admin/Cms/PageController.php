<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Domain\Cms\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('slug')->get();

        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:200', 'unique:pages,slug', 'regex:/^[a-z0-9\-\/]+$/'],
            'title' => ['required', 'array'],
            'title.*' => ['nullable', 'string', 'max:200'],
            'meta_title' => ['nullable', 'array'],
            'meta_title.*' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'array'],
            'meta_description.*' => ['nullable', 'string', 'max:300'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $page = new Page;
        $page->slug = $data['slug'];
        $page->title = $data['title'];
        $page->meta_title = $data['meta_title'] ?? null;
        $page->meta_description = $data['meta_description'] ?? null;
        $page->status = $data['status'];
        $page->save();

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        $sections = $page->sections();
        $allTypes = config('sections', []);

        // Fixed sections (everything except custom_html) not already attached to this page
        $reusableSections = \App\Domain\Cms\Models\PageSection::whereNotIn('id', $page->section_ids ?? [])
            ->where('type', '!=', 'custom_html')
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        // Available types to CREATE:
        //   - custom_html — always creatable
        //   - other types — only if no section of that type exists yet anywhere
        $existingTypes = \App\Domain\Cms\Models\PageSection::pluck('type')->unique();

        $availableTypes = collect($allTypes)->filter(fn ($_cfg, $type) => $type === 'custom_html' || ! $existingTypes->contains($type));

        return view('admin.cms.pages.edit', compact('page', 'sections', 'availableTypes', 'reusableSections'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9\-\/]+$/', 'unique:pages,slug,'.$page->id],
            'title' => ['required', 'array'],
            'title.*' => ['nullable', 'string', 'max:200'],
            'meta_title' => ['nullable', 'array'],
            'meta_title.*' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'array'],
            'meta_description.*' => ['nullable', 'string', 'max:300'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $page->slug = $data['slug'];
        $page->title = $data['title'];
        $page->meta_title = $data['meta_title'] ?? null;
        $page->meta_description = $data['meta_description'] ?? null;
        $page->status = $data['status'];
        $page->save();

        return redirect()->route('admin.cms.pages.edit', $page)->with('success', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        if ($page->is_system) {
            return back()->with('error', 'System pages cannot be deleted.');
        }

        $page->delete();

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted.');
    }

    public function preview(Page $page)
    {
        $sections = $page->enabledSections();

        return view('theme::cms.page-preview', compact('page', 'sections'));
    }
}

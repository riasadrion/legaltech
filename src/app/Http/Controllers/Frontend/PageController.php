<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        $sections = $page->enabledSections();

        return view('theme::cms.page', compact('page', 'sections'));
    }

    public function home()
    {
        $page = Page::where('slug', 'home')->published()->first();

        if (! $page) {
            return view('theme::cms.home-fallback');
        }

        $sections = $page->enabledSections();

        return view('theme::cms.page', compact('page', 'sections'));
    }
}

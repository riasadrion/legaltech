<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Cms\Models\Page;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        $sections = $page->sections()->where('enabled', true)->orderBy('sort')->get();

        return view('theme::cms.page', compact('page', 'sections'));
    }

    public function home()
    {
        $page = Page::where('slug', 'home')->published()->first();

        if (! $page) {
            return view('theme::cms.home-fallback');
        }

        $sections = $page->sections()->where('enabled', true)->orderBy('sort')->get();

        return view('theme::cms.page', compact('page', 'sections'));
    }
}

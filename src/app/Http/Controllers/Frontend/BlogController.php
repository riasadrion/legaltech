<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;

class BlogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $posts = BlogPost::published()
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image', 'published_at', 'blog_category_id'])
            ->with('category:id,name,slug')
            ->when($search !== '', function ($query) use ($search) {
                $locale = app()->getLocale();
                $term = '%'.mb_strtolower($search).'%';
                $query->where(function ($q) use ($term, $locale) {
                    $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"'))) like ?", [$term])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(excerpt, '$.\"{$locale}\"'))) like ?", [$term]);
                });
            })
            ->latest('published_at')
            ->paginate(13)
            ->withQueryString();

        $categories = BlogCategory::withCount(['posts' => fn ($q) => $q->published()])->orderBy('sort')->get();

        return view('theme::blog.index', compact('posts', 'categories', 'search'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['category:id,name,slug', 'tags:id,name,slug'])
            ->firstOrFail();

        $related = BlogPost::published()
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image', 'published_at', 'blog_category_id'])
            ->where('id', '!=', $post->id)
            ->with('category:id,name,slug')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('theme::blog.show', compact('post', 'related'));
    }

    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::published()
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image', 'published_at', 'blog_category_id'])
            ->where('blog_category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('theme::blog.category', compact('category', 'posts'));
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()->published()
            ->select(['blog_posts.id', 'slug', 'title', 'excerpt', 'cover_image', 'published_at', 'blog_category_id'])
            ->with('category:id,name,slug')
            ->latest('published_at')
            ->paginate(12);

        return view('theme::blog.tag', compact('tag', 'posts'));
    }

    public function rss()
    {
        $posts = BlogPost::published()->select(['id', 'slug', 'title', 'excerpt', 'body', 'published_at'])->latest('published_at')->limit(20)->get();

        return response()->view('theme::blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}

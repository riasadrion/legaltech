<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Models\BlogPost;
use App\Domain\Blog\Models\Tag;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::withCount(['posts' => fn ($q) => $q->published()])->orderBy('sort')->get();

        return view('theme::blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->with(['category', 'author', 'tags'])->firstOrFail();

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('theme::blog.show', compact('post', 'related'));
    }

    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::published()
            ->where('blog_category_id', $category->id)
            ->with(['author', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        return view('theme::blog.category', compact('category', 'posts'));
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(12);

        return view('theme::blog.tag', compact('tag', 'posts'));
    }

    public function rss()
    {
        $posts = BlogPost::published()->with('author')->latest('published_at')->limit(20)->get();

        return response()->view('theme::blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}

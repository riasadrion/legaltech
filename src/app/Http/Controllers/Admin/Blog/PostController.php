<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Models\BlogPost;
use App\Domain\Blog\Models\Tag;
use App\Domain\Cms\Sanitizer\HtmlPurifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with(['category', 'author'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('category'), fn ($q, $c) => $q->where('blog_category_id', $c))
            ->when($request->input('search'), fn ($q, $s) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$s}%"]))
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        $categories = BlogCategory::orderBy('sort')->get();

        $counts = BlogPost::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => '',           'label' => 'All',        'count' => $counts->sum()],
            ['value' => 'draft',      'label' => 'Draft',      'count' => $counts['draft'] ?? 0],
            ['value' => 'scheduled',  'label' => 'Scheduled',  'count' => $counts['scheduled'] ?? 0],
            ['value' => 'published',  'label' => 'Published',  'count' => $counts['published'] ?? 0],
        ];

        return view('admin.blog.posts.index', compact('posts', 'categories', 'statusPills'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('sort')->get();
        $tags = Tag::orderBy('slug')->get();

        return view('admin.blog.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $post = new BlogPost;
        $this->fill($post, $data, $request);
        $post->author_id = auth('admin')->id();
        $post->save();

        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post created.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::orderBy('sort')->get();
        $tags = Tag::orderBy('slug')->get();

        return view('admin.blog.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $this->validated($request, $post->id);

        $this->fill($post, $data, $request);
        $post->save();

        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post saved.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.blog.posts.index')->with('success', 'Post deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'slug' => ['nullable', 'string', 'max:200', 'unique:blog_posts,slug'.($ignoreId ? ",{$ignoreId}" : '')],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:200'],
            'title.*' => ['nullable', 'string', 'max:200'],
            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'array'],
            'body.*' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'array'],
            'meta_title.*' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'array'],
            'meta_description.*' => ['nullable', 'string', 'max:300'],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'status' => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);
    }

    private function fill(BlogPost $post, array $data, Request $request): void
    {
        $post->slug = $data['slug'] ?: BlogPost::generateSlug($data['title']['en']);

        $post->blog_category_id = $data['blog_category_id'] ?? null;
        $post->title = $data['title'];
        $post->excerpt = $data['excerpt'] ?? null;
        $post->meta_title = $data['meta_title'] ?? null;
        $post->meta_description = $data['meta_description'] ?? null;
        $post->status = $data['status'];
        $post->published_at = $data['published_at'] ?? ($data['status'] === BlogPost::STATUS_PUBLISHED ? now() : null);

        $body = $data['body'] ?? [];
        foreach ($body as $locale => $html) {
            $body[$locale] = HtmlPurifier::purify((string) $html);
        }
        $post->body = $body;
    }
}

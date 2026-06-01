<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('posts')->orderBy('slug')->get();

        return view('admin.blog.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:100', 'unique:tags,slug'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:100'],
            'name.*' => ['nullable', 'string', 'max:100'],
        ]);

        $tag = new Tag;
        $tag->slug = $data['slug'] ?: Str::slug($data['name']['en']);
        $tag->name = $data['name'];
        $tag->save();

        return back()->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}

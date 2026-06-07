<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('sort')->get();

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:100', 'unique:blog_categories,slug'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:100'],
            'name.*' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string', 'max:300'],
            'sort' => ['integer', 'min:0'],
        ]);

        $category = new BlogCategory;
        $category->slug = $data['slug'] ?: Str::slug($data['name']['en']);
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->sort = $data['sort'] ?? 0;
        $category->save();

        return redirect()->route('admin.blog.categories.index')->with('success', 'Category created.');
    }

    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    public function update(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:100', 'unique:blog_categories,slug,'.$category->id],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:100'],
            'name.*' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string', 'max:300'],
            'sort' => ['integer', 'min:0'],
        ]);

        $category->slug = $data['slug'] ?: Str::slug($data['name']['en']);
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->sort = $data['sort'] ?? 0;
        $category->save();

        return redirect()->route('admin.blog.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(BlogCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.blog.categories.index')->with('success', 'Category deleted.');
    }
}

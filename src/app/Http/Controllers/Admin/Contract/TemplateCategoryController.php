<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Domain\Contract\Models\ContractTemplateCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateCategoryController extends Controller
{
    public function index()
    {
        $categories = ContractTemplateCategory::withCount('templates')->orderBy('sort')->get();
        return view('admin.contracts.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.contracts.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        ContractTemplateCategory::create($data);

        return redirect()->route('admin.contracts.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(ContractTemplateCategory $category)
    {
        return view('admin.contracts.categories.edit', compact('category'));
    }

    public function update(Request $request, ContractTemplateCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()->route('admin.contracts.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(ContractTemplateCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.contracts.categories.index')
            ->with('success', 'Category deleted.');
    }
}

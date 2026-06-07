<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('items')->get();

        return view('admin.cms.menus.index', compact('menus'));
    }

    public function edit(Menu $menu)
    {
        $items = $menu->allItems()->with('page')->get();
        $pages = Page::orderBy('slug')->get(['id', 'slug', 'title']);

        return view('admin.cms.menus.edit', compact('menu', 'items', 'pages'));
    }

    public function addItem(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'label' => ['required', 'array'],
            'label.*' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'open_in_new_tab' => ['boolean'],
        ]);

        $maxSort = $menu->allItems()->where('parent_id', $data['parent_id'] ?? null)->max('sort') ?? -1;

        $item = new MenuItem;
        $item->menu_id = $menu->id;
        $item->parent_id = $data['parent_id'] ?? null;
        $item->label = $data['label'];
        $item->url = $data['url'] ?? null;
        $item->page_id = $data['page_id'] ?? null;
        $item->open_in_new_tab = $data['open_in_new_tab'] ?? false;
        $item->sort = $maxSort + 1;
        $item->save();

        return back()->with('success', 'Menu item added.');
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $item)
    {
        abort_if($item->menu_id !== $menu->id, 404);

        $data = $request->validate([
            'label' => ['required', 'array'],
            'label.*' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'open_in_new_tab' => ['boolean'],
        ]);

        $item->label = $data['label'];
        $item->url = $data['url'] ?? null;
        $item->page_id = $data['page_id'] ?? null;
        $item->open_in_new_tab = $data['open_in_new_tab'] ?? false;
        $item->save();

        return back()->with('success', 'Menu item updated.');
    }

    public function destroyItem(Menu $menu, MenuItem $item)
    {
        abort_if($item->menu_id !== $menu->id, 404);

        $item->delete(); // cascades to children

        return back()->with('success', 'Item removed.');
    }

    public function reorder(Request $request, Menu $menu)
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->input('order') as $sort => $id) {
            $menu->allItems()->where('id', $id)->update(['sort' => $sort]);
        }

        return response()->json(['ok' => true]);
    }
}

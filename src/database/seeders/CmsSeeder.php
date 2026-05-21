<?php

namespace Database\Seeders;

use App\Domain\Cms\Models\Menu;
use App\Domain\Cms\Models\MenuItem;
use App\Domain\Cms\Models\Page;
use App\Domain\Cms\Models\PageSection;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPages();
        $this->seedMenus();
    }

    private function seedPages(): void
    {
        $systemPages = [
            [
                'slug' => 'home',
                'title' => ['en' => 'Home'],
                'status' => 'published',
                'sections' => ['hero', 'features', 'cta'],
            ],
            [
                'slug' => 'about',
                'title' => ['en' => 'About Us'],
                'status' => 'published',
                'sections' => ['content'],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => ['en' => 'Privacy Policy'],
                'status' => 'published',
                'sections' => ['content'],
            ],
            [
                'slug' => 'terms',
                'title' => ['en' => 'Terms of Service'],
                'status' => 'published',
                'sections' => ['content'],
            ],
            [
                'slug' => 'refund-policy',
                'title' => ['en' => 'Refund Policy'],
                'status' => 'published',
                'sections' => ['content'],
            ],
            [
                'slug' => 'cookie-policy',
                'title' => ['en' => 'Cookie Policy'],
                'status' => 'published',
                'sections' => ['content'],
            ],
        ];

        foreach ($systemPages as $i => $pageData) {
            $page = Page::firstOrNew(['slug' => $pageData['slug']]);
            $page->title = $pageData['title'];
            $page->status = $pageData['status'];
            $page->is_system = true;
            $page->save();

            // Add sections if none exist
            if ($page->sections()->count() === 0) {
                foreach ($pageData['sections'] as $sort => $type) {
                    $section = new PageSection;
                    $section->page_id = $page->id;
                    $section->type = $type;
                    $section->data = config('sections.'.$type.'.default_data', []);
                    $section->enabled = true;
                    $section->sort = $sort;
                    $section->save();
                }

                if ($pageData['slug'] === 'home') {
                    $section = new PageSection;
                    $section->page_id = $page->id;
                    $section->type = 'custom_html';
                    $section->data = ['en' => ['title' => 'Custom HTML demo', 'body' => '<p>This is a <strong>Custom HTML</strong> block. Edit or remove it from the admin panel.</p>']];
                    $section->enabled = true;
                    $section->sort = count($pageData['sections']);
                    $section->save();
                }
            }
        }
    }

    private function seedMenus(): void
    {
        $handles = [
            'header' => ['en' => 'Header'],
            'footer' => ['en' => 'Footer'],
            'mobile' => ['en' => 'Mobile'],
        ];

        foreach ($handles as $handle => $name) {
            $menu = Menu::firstOrNew(['handle' => $handle]);
            $menu->name = $name;
            $menu->save();

            if ($menu->allItems()->count() > 0) {
                continue;
            }

            // Default items for header menu
            if ($handle === 'header') {
                $links = [
                    ['label' => ['en' => 'Home'],    'url' => '/'],
                    ['label' => ['en' => 'About'],   'url' => '/p/about'],
                    ['label' => ['en' => 'Contact'],  'url' => '/contact'],
                ];

                foreach ($links as $sort => $link) {
                    $item = new MenuItem;
                    $item->menu_id = $menu->id;
                    $item->label = $link['label'];
                    $item->url = $link['url'];
                    $item->sort = $sort;
                    $item->save();
                }
            }
        }
    }
}

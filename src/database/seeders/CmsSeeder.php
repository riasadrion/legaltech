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
                'sections' => ['hero'],
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

        foreach ($systemPages as $pageData) {
            $page = Page::firstOrNew(['slug' => $pageData['slug']]);
            $page->title = $pageData['title'];
            $page->status = $pageData['status'];
            $page->is_system = true;
            $page->save();

            // Add sections if none exist
            if (empty($page->section_ids)) {
                foreach ($pageData['sections'] as $sort => $type) {
                    $section = new PageSection;
                    $section->type = $type;
                    $section->data = [];
                    $section->enabled = true;
                    $section->sort = $sort;
                    $section->save();

                    $page->addSection($section);
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

            if ($handle === 'header') {
                $links = [
                    ['label' => ['en' => 'Home'],       'url' => '/'],
                    ['label' => ['en' => 'About'],      'url' => '/p/about'],
                    ['label' => ['en' => 'Contact'],    'url' => '/contact'],
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

            if ($handle === 'footer') {
                $columns = [
                    [
                        'label' => ['en' => 'Product'],
                        'children' => [
                            ['label' => ['en' => 'Features'],    'url' => '/#features'],
                            ['label' => ['en' => 'How it works'], 'url' => '/#workflow'],
                            ['label' => ['en' => 'Pricing'],     'url' => '/#pricing'],
                            ['label' => ['en' => 'FAQ'],         'url' => '/#faq'],
                        ],
                    ],
                    [
                        'label' => ['en' => 'Resources'],
                        'children' => [
                            ['label' => ['en' => 'Blog'],    'url' => '/blog'],
                            ['label' => ['en' => 'Contact'], 'url' => '/contact'],
                        ],
                    ],
                    [
                        'label' => ['en' => 'Company'],
                        'children' => [
                            ['label' => ['en' => 'About'],   'url' => '/p/about'],
                            ['label' => ['en' => 'Contact'], 'url' => '/contact'],
                        ],
                    ],
                    [
                        'label' => ['en' => 'Legal'],
                        'children' => [
                            ['label' => ['en' => 'Terms'],   'url' => '/p/terms'],
                            ['label' => ['en' => 'Privacy'], 'url' => '/p/privacy-policy'],
                            ['label' => ['en' => 'Refund'],  'url' => '/p/refund-policy'],
                            ['label' => ['en' => 'Cookies'], 'url' => '/p/cookie-policy'],
                        ],
                    ],
                ];

                foreach ($columns as $colSort => $column) {
                    $parent = new MenuItem;
                    $parent->menu_id = $menu->id;
                    $parent->label = $column['label'];
                    $parent->url = null;
                    $parent->sort = $colSort;
                    $parent->save();

                    foreach ($column['children'] as $childSort => $child) {
                        $item = new MenuItem;
                        $item->menu_id = $menu->id;
                        $item->parent_id = $parent->id;
                        $item->label = $child['label'];
                        $item->url = $child['url'];
                        $item->sort = $childSort;
                        $item->save();
                    }
                }
            }
        }
    }
}

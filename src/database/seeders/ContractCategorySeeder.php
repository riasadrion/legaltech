<?php

namespace Database\Seeders;

use App\Domain\Contract\Models\ContractTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Non-Disclosure Agreements', 'icon' => 'heroicon-o-lock-closed',          'sort' => 1],
            ['name' => 'Employment & HR',            'icon' => 'heroicon-o-user-group',            'sort' => 2],
            ['name' => 'Freelance & Services',       'icon' => 'heroicon-o-briefcase',             'sort' => 3],
            ['name' => 'SaaS & Software',            'icon' => 'heroicon-o-computer-desktop',      'sort' => 4],
            ['name' => 'Real Estate & Leases',       'icon' => 'heroicon-o-home',                  'sort' => 5],
            ['name' => 'Partnerships & Joint Ventures', 'icon' => 'heroicon-o-building-office-2',    'sort' => 6],
            ['name' => 'Sales & Purchase',           'icon' => 'heroicon-o-shopping-cart',         'sort' => 7],
            ['name' => 'Intellectual Property',      'icon' => 'heroicon-o-light-bulb',            'sort' => 8],
            ['name' => 'Finance & Loans',            'icon' => 'heroicon-o-banknotes',             'sort' => 9],
            ['name' => 'General & Miscellaneous',    'icon' => 'heroicon-o-document-text',         'sort' => 10],
        ];

        foreach ($categories as $cat) {
            ContractTemplateCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'sort' => $cat['sort'],
                ]
            );
        }
    }
}

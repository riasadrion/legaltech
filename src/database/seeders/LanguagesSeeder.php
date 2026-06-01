<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'direction' => 'ltr',
                'is_default' => true,
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'code' => 'ar',
                'direction' => 'rtl',
                'is_default' => false,
                'is_active' => true,
                'sort' => 2,
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(['code' => $language['code']], $language);
        }
    }
}

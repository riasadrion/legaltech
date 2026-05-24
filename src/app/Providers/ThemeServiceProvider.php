<?php

namespace App\Providers;

use App\Domain\Settings\Facades\Settings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $theme = $this->activeTheme();

        $this->loadViewsFrom(resource_path("views/themes/{$theme}"), 'theme');

        // Register anonymous components from the active theme:
        //   <x-theme::plan-card />
        Blade::anonymousComponentNamespace("themes.{$theme}.components", 'theme');

        // Expose current theme name to all views
        view()->share('activeTheme', $theme);
    }

    private function activeTheme(): string
    {
        try {
            return Settings::string('site.theme', 'default');
        } catch (\Throwable) {
            return 'default';
        }
    }
}

<?php

namespace App\Providers;

use App\Facades\Settings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $theme = $this->activeTheme();

        $this->loadViewsFrom(resource_path("views/themes/{$theme}"), 'theme');

        // Prepend the theme root to view.paths so the framework's error-view
        // resolver (which builds the "errors::" namespace from config('view.paths')
        // + '/errors') finds the themed pages in views/themes/{theme}/errors/.
        $themePath = resource_path("views/themes/{$theme}");
        config(['view.paths' => array_merge([$themePath], config('view.paths', []))]);
        view()->addLocation($themePath);

        Blade::anonymousComponentNamespace("themes.{$theme}.components", 'theme');
        Blade::anonymousComponentPath(resource_path("views/themes/{$theme}/components"));

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

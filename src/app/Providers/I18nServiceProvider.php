<?php

namespace App\Providers;

use App\Http\Middleware\ResolveLocale;
use Illuminate\Support\ServiceProvider;

class I18nServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('language', ResolveLocale::class);
    }
}

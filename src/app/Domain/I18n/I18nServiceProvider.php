<?php

namespace App\Domain\I18n;

use App\Domain\I18n\Middleware\ResolveLocale;
use Illuminate\Support\ServiceProvider;

class I18nServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('language', ResolveLocale::class);
    }
}

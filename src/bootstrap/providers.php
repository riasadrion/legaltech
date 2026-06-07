<?php

use App\Providers\AppServiceProvider;
use App\Providers\I18nServiceProvider;
use App\Providers\PaymentServiceProvider;
use App\Providers\ThemeServiceProvider;

return [
    AppServiceProvider::class,
    I18nServiceProvider::class,
    PaymentServiceProvider::class,
    ThemeServiceProvider::class,
];

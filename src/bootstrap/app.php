<?php

use App\Http\Middleware\CheckMaintenance;
use App\Http\Middleware\DemoMode;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\ExpireSubscriptions;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\RequireKyc;
use App\Http\Middleware\RequirePermission;
use App\Http\Middleware\RequireVerifiedEmail;
use App\Http\Middleware\RequireVerifiedPhone;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/frontend.php',
            __DIR__.'/../routes/auth.php',
            __DIR__.'/../routes/user.php',
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/catchall.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/webhooks.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
        $middleware->web(prepend: [
            ForceHttps::class,
        ]);
        $middleware->alias([
            'permission' => RequirePermission::class,
            'auth.admin' => Authenticate::class,
            'account.active' => EnsureAccountIsActive::class,
            'subscription.expire' => ExpireSubscriptions::class,
            'maintenance' => CheckMaintenance::class,
            'demo' => DemoMode::class,
            'verified.email' => RequireVerifiedEmail::class,
            'verified.phone' => RequireVerifiedPhone::class,
            'kyc' => RequireKyc::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

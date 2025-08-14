<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\\Http\\Controllers')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace('App\\Http\\Controllers')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace('App\\Http\\Controllers')
                ->group(base_path('routes/publisher.php'));

            Route::middleware('web')
                ->namespace('App\\Http\\Controllers')
                ->group(base_path('routes/frontend.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'protectLoginMiddleware' => \App\Http\Middleware\ProtectLoginMiddleware::class,
            'desktop_token' => \App\Http\Middleware\DesktopTokenValidation::class,
            'api.library' => \App\Http\Middleware\AuthApiLibrary::class,
            'protect.menu' => \App\Http\Middleware\ProtectMenuMiddleware::class,
            'authenticate.api' => \App\Http\Middleware\AuthenticateApiMiddleware::class,
            'strip_tags' => \App\Http\Middleware\StripTagsFromInput::class,
            'throttle_form' => \App\Http\Middleware\ThrottleForm::class,
            'log_form' => \App\Http\Middleware\LogForm::class,
        ]);

        $middleware->web(append: [
            VerifyCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

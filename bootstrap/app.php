<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\App;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function ($router) {
            Route::prefix('crud')
                ->middleware('api')
                ->name('crud')
                ->group(base_path('routes/crud.php'));
            Route::prefix('auth')
                ->middleware('api')
                ->name('auth')
                ->group(base_path('routes/auth.php'));
            Route::prefix('lap')
                ->middleware('api')
                ->name('lap')
                ->group(base_path('routes/lap.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Menghapus throttle bawaan
        $middleware->remove(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        // mendaftarkan middleware baru
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'mythrottle' => \App\Http\Middleware\MyThorotleMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

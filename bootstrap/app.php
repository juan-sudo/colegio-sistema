<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\SessionManager;

return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
                    web: __DIR__.'/../routes/web.php',
                    api: __DIR__.'/../routes/api.php',
                    commands: __DIR__.'/../routes/console.php',
                    health: '/up',
                )
    ->withMiddleware(function (Middleware $middleware) {
        // Confiar en el proxy de Render para que Laravel detecte correctamente
                     // que las peticiones llegan por HTTPS (X-Forwarded-Proto) y no genere
                     // URLs/formularios en http:// (Render termina TLS antes de la app).
                     $middleware->trustProxies(at: '*');

                     $middleware->alias([
                                        'role' => \App\Http\Middleware\RoleMiddleware::class,
                                        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
                    \Maatwebsite\Excel\ExcelServiceProvider::class,
                    \Laravel\Sanctum\SanctumServiceProvider::class,
                    ])
    ->create();

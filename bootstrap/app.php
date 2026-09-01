<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->trustProxies(at: '*');

        $middleware->trustHosts(at: [
            'rkhnmjtq-8000.brs.devtunnels.ms',
            'rkhnmjtq.brs.devtunnels.ms',
            'localhost',
            '127.0.0.1',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
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

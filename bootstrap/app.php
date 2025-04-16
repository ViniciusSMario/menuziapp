<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\InjectTenant::class);
        $middleware->alias([
            'tenant.auth' => \App\Http\Middleware\TenantAuthenticate::class,
            'subscribed' => \App\Http\Middleware\EnsureSubscriptionIsActive::class,
            'check.subscription' => \App\Http\Middleware\TenantSubscriptionActive::class,
            'isTenantActive' => \App\Http\Middleware\TenantSubscriptionIsValid::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

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
        // Registrar los middlewares de Spatie Laravel Permission
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class, // Cambiado de Middlewares a Middleware
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class, // Cambiado de Middlewares a Middleware
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class, // Cambiado de Middlewares a Middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
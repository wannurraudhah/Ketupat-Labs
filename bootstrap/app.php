<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.check' => \App\Http\Middleware\CheckAuth::class,
        ]);
        
        // Enable sessions for API routes (needed for session-based authentication)
        // Add session middleware to API routes since we're using session-based auth
        // This allows API routes to set and read sessions
        $middleware->api(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        ]);
        
        // Web routes in Laravel 11 automatically get the 'web' middleware group
        // which includes session middleware, so we don't need to add it explicitly
        // The session cookie set by API routes will be accessible to web routes
        // because they share the same domain and the cookie path is set to '/'
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

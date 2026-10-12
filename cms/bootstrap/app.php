<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The site sits behind a Cloudflare tunnel whose connector (cloudflared) runs on this same server.
        // The tunnel calls the origin as "localhost:8080" and puts the real name in X-Forwarded-Host, so the
        // forwarded host, scheme, port and client IP are trusted, but only from the local machine. Anyone else
        // (for example a LAN client) cannot choose the host used for URLs by sending these headers.
        $middleware->trustProxies(
            at: ['127.0.0.1', '::1'],
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

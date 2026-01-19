<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * Dieksekusi untuk SEMUA request
     */
    protected $middleware = [
        // Trust proxy/load balancer
        \App\Http\Middleware\TrustProxies::class,

        // Handle CORS
        \Illuminate\Http\Middleware\HandleCors::class,

        // Prevent request saat maintenance
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validasi ukuran request
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim string input
        \App\Http\Middleware\TrimStrings::class,

        // Convert empty string ke null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware groups.
     */
    protected $middlewareGroups = [

        /**
         * WEB middleware
         */
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        /**
         * API middleware
         */
        'api' => [
            // Rate limit API
            'throttle:api',

            // Route model binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // 🔑 SET ORGANIZATION CONTEXT
            \App\Http\Middleware\SetOrganizationContext::class,
        ],
    ];

    /**
     * Route middleware (dipakai per route)
     */
    protected $routeMiddleware = [

        // Auth
        'auth' => \App\Http\Middleware\Authenticate::class,

        // Authorization
        'can' => \Illuminate\Auth\Middleware\Authorize::class,

        // Guest only
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Signed URL
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,

        // Rate limiting
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // Email verified
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

 
        'organization' => \App\Http\Middleware\SetOrganizationContext::class,

 
    ];
}

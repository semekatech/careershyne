<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '*',
        'auth/*',
        '/dashboard/stats',
        'cv-orders/*',
        'payments/*',
        'orders/*',
        'users/*',
        'jobs/*',
        'ai/*',
        'whatsapp-bot',
        'log-visitor',

    ];

}

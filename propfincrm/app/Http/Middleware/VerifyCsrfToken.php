<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'notifications/markread',
        // This endpoint is protected by EnsureSchedulerToken instead of a browser session.
        'curl/scheduler/auto_assign',
    ];
}

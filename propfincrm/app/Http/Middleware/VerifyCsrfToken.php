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
        // External providers cannot carry a browser CSRF token. These requests
        // are authenticated with the provider-specific signature/token checks
        // in LeadWebhookController before a lead is accepted.
        'webhooks/meta/*',
        'webhooks/leads/*',
        // This endpoint is protected by EnsureSchedulerToken instead of a browser session.
        'curl/scheduler/auto_assign',
    ];
}

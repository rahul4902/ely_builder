<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\LogLastUserActivity::class,
            \App\Http\Middleware\ResolveActiveCompany::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'client.create' => [ \App\Http\Middleware\Client\CanClientCreate::class ],
        'client.update' => [ \App\Http\Middleware\Client\CanClientUpdate::class ],
        'user.create' => [ \App\Http\Middleware\User\CanUserCreate::class ],
        'user.update' => [ \App\Http\Middleware\User\CanUserUpdate::class ],
        'task.create' => [ \App\Http\Middleware\Task\CanTaskCreate::class ],
        'task.update.status' => [ \App\Http\Middleware\Task\CanTaskUpdateStatus::class ],
        'task.assigned' => [ \App\Http\Middleware\Task\IsTaskAssigned::class ],
        'lead.create' => [ \App\Http\Middleware\Lead\CanLeadCreate::class ],
        'lead.assigned' => [ \App\Http\Middleware\Lead\IsLeadAssigned::class ],
        'lead.update.status' => [ \App\Http\Middleware\Lead\CanLeadUpdateStatus::class ],
        'user.is.admin' => [ \App\Http\Middleware\RedirectIfNotAdmin::class ],
        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'role' => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustRole::class,
        'permission' => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustPermission::class,
        'ability' => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustAbility::class,
        'scheduler.token' => \App\Http\Middleware\EnsureSchedulerToken::class,
        'company.admin' => \App\Http\Middleware\EnsureCompanyAdmin::class,
        'platform.super_admin' => \App\Http\Middleware\EnsurePlatformSuperAdmin::class,
        'api.tenant' => \App\Http\Middleware\ApiTenantMiddleware::class,
    ];
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\IntegrationPolicy;
use App\Policies\LeadPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Lead::class => LeadPolicy::class,
        Client::class => ClientPolicy::class,
        Task::class => TaskPolicy::class,
        User::class => UserPolicy::class,
        Integration::class => IntegrationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user && ($user->hasRole('administrator') || $user->hasRole('super_administrator') || $user->isCompanyAdmin())) {
                return true;
            }
        });
    }
}

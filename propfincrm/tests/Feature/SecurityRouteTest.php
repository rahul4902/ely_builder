<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityRouteTest extends TestCase
{
    public function test_scheduler_endpoint_rejects_requests_without_a_token(): void
    {
        $route = $this->routeFor('curl/scheduler/auto_assign');

        $this->assertSame(['POST'], $route->methods());
        $this->assertContains('scheduler.token', $route->middleware());
    }

    public function test_cache_endpoint_requires_authentication(): void
    {
        $route = $this->routeFor('cache');

        $this->assertContains('auth', $route->middleware());
        $this->assertContains('user.is.admin', $route->middleware());
    }

    public function test_state_changing_master_routes_reject_get_requests(): void
    {
        $this->assertSame(['POST'], $this->routeFor('delete_project')->methods());
        $this->assertSame(['POST'], $this->routeFor('delete_lead')->methods());
    }

    private function routeFor(string $uri)
    {
        foreach (app('router')->getRoutes()->getRoutes() as $route) {
            if ($route->uri() === $uri) {
                return $route;
            }
        }

        $this->fail("Route [{$uri}] was not registered.");
    }
}

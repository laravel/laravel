<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that routes are registered correctly.
     */
    public function test_routes_are_registered(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function($route) {
            return $route->uri();
        })->toArray();
        
        // Verify key routes are registered
        $this->assertContains('login', $routes);
        $this->assertContains('dashboard', $routes);
        $this->assertContains('pegawai', $routes);
    }
}

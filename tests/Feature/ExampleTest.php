<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        config([
            'app.name' => 'Starter Kit',
            'app.locale' => 'fr_CA',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="fr-CA">', false);
        $response->assertSee('<title>Starter Kit</title>', false);
    }
}

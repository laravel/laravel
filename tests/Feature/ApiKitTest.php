<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiKitTest extends TestCase
{
    public function test_status_endpoint_returns_api_metadata(): void
    {
        $this->getJson('/api/v1/status')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', config('api.name'))
            ->assertJsonPath('data.version', config('api.version'));
    }

    public function test_api_requests_are_forced_to_json_responses(): void
    {
        $this->post('/api/v1/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'email',
                    'password',
                ],
                'code',
            ]);
    }

    public function test_missing_api_routes_return_json_envelope(): void
    {
        $this->get('/api/v1/missing-route')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'NOT_FOUND');
    }
}

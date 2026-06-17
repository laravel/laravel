<?php

namespace Tests\Feature;

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_success_response_structure()
    {
        Route::get('/test-success', [TestController::class, 'success']);

        $response = $this->getJson('/test-success');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                ],
                'meta',
            ])
            ->assertJsonPath('data.id', 1)
            ->assertJsonPath('data.name', 'test')
            ->assertJsonPath('meta.page', 1);
    }

    public function test_error_response_structure()
    {
        Route::get('/test-error', [TestController::class, 'error']);

        $response = $this->getJson('/test-error');

        $response->assertStatus(400)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'code',
            ])
            ->assertJsonPath('errors.field.0', 'invalid')
            ->assertJsonPath('code', 'VALIDATION_ERROR');
    }
}

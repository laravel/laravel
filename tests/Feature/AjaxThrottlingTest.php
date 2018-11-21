<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Collection;

class AjaxThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_TrottledEndpointRejectsAfter60Requests()
    {
        $this->withoutExceptionHandling();

        // Given
        Collection::times(60, function () {
            $response = $this->post(route('password-resets.store'), [ 'email' => 'example@example.com' ]);
            $response->assertJson([ 'message' => trans('accounts.passwords.sent') ]);
        });

        // Then
        $this->expectException(ThrottleRequestsException::class);

        // When
        $this->post(route('password-resets.store'), [ 'email' => 'example@example.com' ]);
    }

    public function test_TrottledEndpointDoesntRejectSecondUser()
    {
        $this->withoutExceptionHandling();

        // Given
        Collection::times(60, function () {
            $response = $this->post(route('password-resets.store'), [ 'email' => 'example@example.com' ]);
            $response->assertJson([ 'message' => trans('accounts.passwords.sent') ]);
        });

        // When
        $response = $this->post(route('password-resets.store'), [ 'email' => 'example@example.com' ], ['REMOTE_ADDR' => '10.1.0.1']);

        // Then
        $response->assertJson([ 'message' => trans('accounts.passwords.sent') ]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use function Pest\Laravel\getJson;

describe('users', function (): void {
    /** @see GetUserController */
    it('retrieves a user and returns a successful response', function (): void {
        $existingUser = UserFactory::new()->createOne();

        $response = getJson("api/users/$existingUser->id")
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll(
                        UserResource::make($existingUser)->resolve()
                    )
                )
            );
    });

    it('returns a 404 response when user is not found', function (): void {
        $nonExistentUserId = 99999;

        getJson("api/users/{$nonExistentUserId}")
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND);
    });
});

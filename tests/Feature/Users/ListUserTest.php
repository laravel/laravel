<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Backoffice\Users\App\Transformers\UserTransformer;
use function Pest\Laravel\getJson;

describe('users', function () {
    /** @see StoreUserController */
    it('can list users successfully', function () {
        $users = UserFactory::new()
            ->createMany(5);

        getJson(url('/api/users'))
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('status', JsonResponse::HTTP_OK)
                    ->where('success', true)
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->whereAll(
                            transformation($users, UserTransformer::class)->transform() ?? []
                        )
                    )
            );
    });
});

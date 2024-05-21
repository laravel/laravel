<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Backoffice\Users\App\Controllers\StoreUserController;
use Lightit\Backoffice\Users\App\Notifications\UserRegistered;
use Lightit\Backoffice\Users\App\Transformers\UserTransformer;
use Lightit\Backoffice\Users\Domain\Models\User;
use Tests\RequestFactories\StoreUserRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertTrue;

describe('users', function () {
    /** @see StoreUserController */
    it('can create a user successfully', function () {
        Notification::fake();

        $data = StoreUserRequestFactory::new()->create([
            'password' => 'passw0rd',
        ]);

        $response = postJson(url('/api/users'), $data);

        $user = User::query()
            ->where('email', $data['email'])
            ->firstOrFail();

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('status', JsonResponse::HTTP_CREATED)
                    ->where('success', true)
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->whereAll(
                            transformation($user, UserTransformer::class)->transform() ?? []
                        )
                    )
            );

        assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        assertTrue(Hash::check('passw0rd', $user->password));

        Notification::assertSentTo($user, UserRegistered::class);
    });
});

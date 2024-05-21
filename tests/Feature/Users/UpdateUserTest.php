<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Backoffice\Users\App\Controllers\UpdateUserController;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use Lightit\Backoffice\Users\Domain\Models\User;
use Tests\RequestFactories\StoreUserRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

beforeEach(fn () => Notification::fake());

describe('users', function (): void {
    /** @see UpdateUserController */
    it('can create a user successfully', function (): void {
        $user = UserFactory::new()->createOne([
            'name' => 'old',
        ]);

        $data = StoreUserRequestFactory::new()->create([
            'name' => 'Updated',
        ]);

        $response = putJson(url("/api/users/$user->id"), $data);

        $user = User::query()
            ->where('name', $data['name'])
            ->firstOrFail();

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll(
                        UserResource::make($user)->resolve()
                    )
                )
            );

        assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email_address'],
        ]);

        expect(Hash::check('passw0rd', $user->password))->toBeTrue();
    });

    it('can edit a user with the same registered email', function (): void {
        $existingUser = UserFactory::new()->createOne();

        $data = StoreUserRequestFactory::new()->create([
            'email_address' => $existingUser->email,
        ]);

        $response = putJson(url("/api/users/$existingUser->id"), $data);

        $response->assertOk();

        assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email_address'],
        ]);
    });

    it('cannot create a user with invalid data', function (): void {
        $existingUser = UserFactory::new()->createOne();

        $data = [
            'name' => '',
            'email_address' => 'not-an-email',
            'password' => 'short',
        ];

        $response = putJson(url("/api/users/$existingUser->id"), $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email_address', 'password'], 'error.fields');
    });
});

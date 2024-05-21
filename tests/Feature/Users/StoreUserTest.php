<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Backoffice\Users\App\Controllers\StoreUserController;
use Lightit\Backoffice\Users\App\Notifications\UserRegisteredNotification;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use Lightit\Backoffice\Users\Domain\Models\User;
use Tests\RequestFactories\StoreUserRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

beforeEach(fn () => Notification::fake());

describe('users', function (): void {
    /** @see StoreUserController */
    it('can create a user successfully', function (): void {
        $data = StoreUserRequestFactory::new()->create([
            'password' => 'passw0rd',
        ]);

        $response = postJson(url('/api/users'), $data);

        $user = User::query()
            ->where('email', $data['email_address'])
            ->firstOrFail();

        $response
            ->assertCreated()
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

        Notification::assertSentTo($user, UserRegisteredNotification::class);
    });

    it('cannot create a user with an already registered email', function (): void {
        $existingUser = UserFactory::new()->createOne();

        $data = StoreUserRequestFactory::new()->create([
            'email_address' => $existingUser->email,
        ]);

        $response = postJson(url('/api/users'), $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email_address'], 'error.fields');

        assertDatabaseMissing('users', [
            'name' => $data['name'],
            'email' => $data['email_address'],
        ]);
    });

    it('cannot create a user with invalid data', function (): void {
        $data = [
            'name' => '',
            'email_address' => 'not-an-email',
            'password' => 'short',
        ];

        $response = postJson(url('/api/users'), $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email_address', 'password'], 'error.fields');
    });

    it('triggers user registration notification', function (): void {
        Notification::fake();

        $data = StoreUserRequestFactory::new()->create();

        postJson(url('/api/users'), $data);

        $user = User::query()->where('email', $data['email_address'])->firstOrFail();

        Notification::assertSentTo($user, UserRegisteredNotification::class);
    });
});

<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Users\App\Controllers\StoreUserController;
use Lightit\Users\App\Notifications\UserRegisteredNotification;
use Lightit\Users\App\Resources\UserResource;
use Lightit\Users\Domain\Models\User;
use Tests\RequestFactories\StoreUserRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

function getLongName(): string
{
    return Str::repeat(string: 'name', times: random_int(min: 30, max: 50));
}

function getATakenEmail(): string
{
    $takenEmail = 'taken@example.com';
    UserFactory::new()->createOne(['email' => $takenEmail]);

    return $takenEmail;
}

dataset(name: 'validation-rules', dataset: [
    'name is required' => ['name', ''],
    'name be a string' => ['name', ['array']],
    'name not too short' => ['name', 'ams'],
    'name not too long' => ['name', getLongName()],

    'email is required' => ['email_address', ''],
    'email be valid' => ['email_address', 'esthernjerigmail.com'],
    'email not too long' => ['email_address', fn (): string => getLongName() . '@gmail.com'],
    'email be unique' => ['email_address', fn (): string => getATakenEmail()],

    'password is required' => ['password', ''],
    'password be >=8 chars' => ['password', 'Hf^gsg8'],
    'password be uncompromised' => ['password', 'password'],
    'password not too long' => ['password', fn (): string => getLongName()],
]);

beforeEach(fn () => Notification::fake());

describe('users', function (): void {
    /** @see StoreUserController */
    it(description: 'can create a user successfully', closure: function (): void {
        $data = StoreUserRequestFactory::new()->create([
            'password' => '>e$pV4chNFcJoAB%X#{',
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

        expect(Hash::check('>e$pV4chNFcJoAB%X#{', $user->password))->toBeTrue();

        Notification::assertSentTo($user, UserRegisteredNotification::class);
    });

    it(description: 'cannot create a user with an already registered email', closure: function (): void {
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

    it('cannot create a user with invalid data', closure: function (string $field, string|array $value): void {
        $data = StoreUserRequestFactory::new()->create();

        $response = postJson(url('/api/users'), [...$data, $field => $value]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field], 'error.fields');
    })->with('validation-rules');

    it(description: 'triggers user registration notification', closure: function (): void {
        Notification::fake();

        $data = StoreUserRequestFactory::new()->create();

        postJson(url('/api/users'), $data);

        $user = User::query()->where('email', $data['email_address'])->firstOrFail();

        Notification::assertSentTo($user, UserRegisteredNotification::class);
    });
});

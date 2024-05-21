<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Database\Factories\UserFactory;
use function Pest\Laravel\getJson;

describe('users', function (): void {
    /** @see StoreUserController */
    it('can list users successfully', function (): void {
        $users = UserFactory::new()
            ->createMany(5);

        getJson(url('/api/users'))
            ->assertSuccessful()
            ->assertJsonCount(5, 'data');
    });
});

<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use Lightit\Users\App\Controllers\StoreUserController;
use Tests\RequestFactories\StoreUserRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

describe('users', function () {
    /** @see StoreUserController */
    it('can create a user successfully', function () {
        $data = StoreUserRequestFactory::new()->create();

        postJson(url('/api/users'), $data)
            ->assertCreated();

        assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    });
});

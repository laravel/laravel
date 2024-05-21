<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StoreUserRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'email_address' => fake()->email,
            'password' => 'passw0rd',
            'password_confirmation' => 'passw0rd',
        ];
    }
}

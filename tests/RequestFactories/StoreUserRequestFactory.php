<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StoreUserRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'passw0rd',
            'password_confirmation' => 'passw0rd'
        ];
    }
}

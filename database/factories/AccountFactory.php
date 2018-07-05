<?php

use Faker\Generator as Faker;

$factory->define(App\Domain\Accounts\Account::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'verified_at' => now(),
    ];
});

$factory->state(App\Domain\Accounts\Account::class, 'unverified', function (Faker $faker) {
    return [
        'verified_at' => null,
    ];
});

$factory->state(App\Domain\Accounts\Account::class, 'unregistered', function (Faker $faker) {
    return [
        'password' => 'password',
        'password_confirmation' => 'password',
        'remember_token' => null,
        'verified_at' => null,
    ];
});

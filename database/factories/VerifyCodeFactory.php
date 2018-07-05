<?php

use Faker\Generator as Faker;

$factory->define(App\Domain\Accounts\Verification\VerifyCode::class, function (Faker $faker) {
    return [
        'account_id' => function () {
            return factory(App\Domain\Accounts\Account::class)->states('unverified')->create();
        },
        'code' => $faker->sha256(),
        'expires_at' => now()->addHours(1),
    ];
});

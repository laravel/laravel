<?php

use Faker\Generator as Faker;

use Illuminate\Support\Facades\Hash;

$factory->define(App\Domain\Accounts\Verification\VerifyCode::class, function (Faker $faker) {
    return [
        'account_id' => function () {
            return factory(App\Domain\Accounts\Account::class)->states('unverified')->create();
        },
        'code' => 'test',
        'token' => Hash::make('secret'),
        'expired_at' => now()->addHours(1),
    ];
});

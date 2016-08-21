<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,

        // Use a precomputed hash of the word "secret" instead of using bcrypt directly.
        // Since bcrypt is intentionally slow, it can really slow down test suites in
        // large applications that use factories to generate models in many tests.
        'password' => '$2y$10$oPCcCpaPQ69KQ1fdrAIL0eptYCcG/s/NmQZizJfVdB.QOXUn5mGE6',

        'remember_token' => str_random(10),
    ];
});

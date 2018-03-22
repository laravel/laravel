<?php

use Faker\Generator as Faker;

/** @var TYPE_NAME $factory https://laravel.com/docs/5.6/database-testing#writing-factories */
$factory->define(App\Models\DataTables\Exam::class, function (Faker $faker) {
    return [
        'user_id' => random_int(1,100),  // ! database/seeds/DatabaseSeeder 100 users !
        'header' => $faker->name,
        'text' => $faker->text(50),
    ];
});

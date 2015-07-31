<?php

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    public function testFirstObservation()
    {
        $fake = Faker::create();

        $u = new App\User(
        [
            'name' => $fake->name,
            'email' => $fake->email,
            'password' => $fake->word,
        ]);

        $u->save();

        $this->assertTrue($u->observed);
    }

    public function testSecondObservation()
    {
        $fake = Faker::create();

        $u = new App\User([
            'name' => $fake->name,
            'email' => $fake->email,
            'password' => $fake->word,
        ]);

        $u->save();

        $this->assertTrue($u->observed);
    }
}

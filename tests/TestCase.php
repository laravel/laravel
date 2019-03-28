<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /** @var \Faker\Generator */
    protected $faker;

    /**
     * Setup some common functionality/tools such as Faker and Log mocking.
     */
    protected function setUp()
    {
        parent::setUp();

        // configure faker
        $this->faker = Factory::create(config('app.faker_locale'));

        // swap out the logger to a null handler so its not written to disk
        Log::swap(new Logger(config('app.env'), [new NullHandler()]));
    }
}

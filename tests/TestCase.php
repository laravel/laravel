<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        if (config('app.env') !== 'testing') {
            throw new \Exception('APP ENV IS NOT TESTING! (check config:clear)');
        }
        
        if (config('database.default') !== 'sqlite') {
            throw new \Exception('APP TEST DATABASE IS WRONG! (check config:clear)');
        }
    }
}

<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
    
    public function setUp()
    {
        parent::setUp();
        if(method_exists($this, 'before')) {
            $this->app->call([$this, 'before']);
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        if(method_exists($this, 'after')) {
            $this->app->call([$this, 'after']);
        }
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * that is called before every test.
     * to initialise variables, open file connection etc
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        // code
    }
    
    /**
     * that is called after every test.
     * to unset variables, close file connection etc
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        
        // code
    }
    
}

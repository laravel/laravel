<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\FreshDatabase;

class ExampleTest extends TestCase
{
    use FreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

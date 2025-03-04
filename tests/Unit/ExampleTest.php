<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_user_has_a_name(): void
    {
        $user = new User(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->name);
    }
}

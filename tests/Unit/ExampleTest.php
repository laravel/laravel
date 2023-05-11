<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_sum(): void
    {
        $sum = 1 + 1;

        $this->assertEqual(2, $sum);
    }
}

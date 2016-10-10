<?php

/**
 * A base test case with some custom expectations.
 *
 * @author Rouven Weßling
 */
class SwiftMailerTestCase extends \PHPUnit_Framework_TestCase
{
    public static function regExp($pattern)
    {
        if (!is_string($pattern)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        return new PHPUnit_Framework_Constraint_PCREMatch($pattern);
    }

    public function assertIdenticalBinary($expected, $actual, $message = '')
    {
        $constraint = new IdenticalBinaryConstraint($expected);
        self::assertThat($actual, $constraint, $message);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function getMockery($class)
    {
        return \Mockery::mock($class);
    }
}

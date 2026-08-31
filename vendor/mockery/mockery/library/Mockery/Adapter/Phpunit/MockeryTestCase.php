<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Adapter\Phpunit;

use PHPUnit\Framework\TestCase;

abstract class MockeryTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
    use MockeryTestCaseSetUp;

    protected function mockeryTestSetUp()
    {
    }

    protected function mockeryTestTearDown()
    {
    }
}

<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\TestCase;

/**
 * @covers Monolog\Handler\SamplingHandler::handle
 */
class SamplingHandlerTest extends TestCase
{
    public function testHandle()
    {
        $testHandler = new TestHandler();
        $handler = new SamplingHandler($testHandler, 2);
        for ($i = 0; $i < 10000; $i++) {
            $handler->handle($this->getRecord());
        }
        $count = count($testHandler->getRecords());
        // $count should be half of 10k, so between 4k and 6k
        $this->assertLessThan(6000, $count);
        $this->assertGreaterThan(4000, $count);
    }
}

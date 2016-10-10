<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis;

use PredisTestCase;

/**
 *
 */
class ResponseQueuedTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testResponseQueuedClass()
    {
        $queued = new ResponseQueued();

        $this->assertInstanceOf('Predis\ResponseObjectInterface', $queued);
    }

    /**
     * @group disconnected
     */
    public function testToString()
    {
        $queued = new ResponseQueued();

        $this->assertEquals('QUEUED', (string) $queued);
    }

    /**
     * @group disconnected
     */
    public function testQueuedProperty()
    {
        $queued = new ResponseQueued();

        $this->assertTrue(isset($queued->queued));
        $this->assertTrue($queued->queued);
    }
}

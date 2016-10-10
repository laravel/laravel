<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

use PredisTestCase;

/**
 *
 */
class ClientExceptionsTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testDefaultReturnsTrue()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientExceptions();

        $this->assertTrue($option->getDefault($options));
    }
}

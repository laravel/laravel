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
class ResponseErrorTest extends PredisTestCase
{
    const ERR_WRONG_KEY_TYPE = 'ERR Operation against a key holding the wrong kind of value';

    /**
     * @group disconnected
     */
    public function testResponseErrorClass()
    {
        $error = new ResponseError(self::ERR_WRONG_KEY_TYPE);

        $this->assertInstanceOf('Predis\ResponseErrorInterface', $error);
        $this->assertInstanceOf('Predis\ResponseObjectInterface', $error);
    }

    /**
     * @group disconnected
     */
    public function testErrorMessage()
    {
        $error = new ResponseError(self::ERR_WRONG_KEY_TYPE);

        $this->assertEquals(self::ERR_WRONG_KEY_TYPE, $error->getMessage());
    }

    /**
     * @group disconnected
     */
    public function testErrorType()
    {
        $exception = new ResponseError(self::ERR_WRONG_KEY_TYPE);

        $this->assertEquals('ERR', $exception->getErrorType());
    }

    /**
     * @group disconnected
     */
    public function testToString()
    {
        $error = new ResponseError(self::ERR_WRONG_KEY_TYPE);

        $this->assertEquals(self::ERR_WRONG_KEY_TYPE, (string) $error);
    }
}

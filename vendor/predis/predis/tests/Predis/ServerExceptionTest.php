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
class ServerExceptionTest extends PredisTestCase
{
    const ERR_WRONG_KEY_TYPE = 'ERR Operation against a key holding the wrong kind of value';

    /**
     * @group disconnected
     */
    public function testExceptionMessage()
    {
        $this->setExpectedException('Predis\ServerException', self::ERR_WRONG_KEY_TYPE);

        throw new ServerException(self::ERR_WRONG_KEY_TYPE);
    }

    /**
     * @group disconnected
     */
    public function testExceptionClass()
    {
        $exception = new ServerException(self::ERR_WRONG_KEY_TYPE);

        $this->assertInstanceOf('Predis\ServerException', $exception);
        $this->assertInstanceOf('Predis\ResponseErrorInterface', $exception);
        $this->assertInstanceOf('Predis\ResponseObjectInterface', $exception);
        $this->assertInstanceOf('Predis\PredisException', $exception);
    }

    /**
     * @group disconnected
     */
    public function testErrorType()
    {
        $exception = new ServerException(self::ERR_WRONG_KEY_TYPE);

        $this->assertEquals('ERR', $exception->getErrorType());
    }

    /**
     * @group disconnected
     */
    public function testToResponseError()
    {
        $exception = new ServerException(self::ERR_WRONG_KEY_TYPE);
        $error = $exception->toResponseError();

        $this->assertInstanceOf('Predis\ResponseError', $error);

        $this->assertEquals($exception->getMessage(), $error->getMessage());
        $this->assertEquals($exception->getErrorType(), $error->getErrorType());
    }
}

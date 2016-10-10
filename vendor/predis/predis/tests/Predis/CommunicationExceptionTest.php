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
use Predis\Connection\SingleConnectionInterface;

/**
 *
 */
class CommunicationExceptionTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExceptionMessage()
    {
        $message = 'Connection error message.';
        $connection = $this->getMockedConnectionBase();
        $exception = $this->getException($connection, $message);

        $this->setExpectedException('Predis\CommunicationException', $message);

        throw $exception;
    }

    /**
     * @group disconnected
     */
    public function testExceptionConnection()
    {
        $connection = $this->getMockedConnectionBase();
        $exception = $this->getException($connection, 'ERROR MESSAGE');

        $this->assertSame($connection, $exception->getConnection());
    }

    /**
     * @group disconnected
     */
    public function testShouldResetConnection()
    {
        $connection = $this->getMockedConnectionBase();
        $exception = $this->getException($connection, 'ERROR MESSAGE');

        $this->assertTrue($exception->shouldResetConnection());
    }

    /**
     * @group disconnected
     * @expectedException Predis\CommunicationException
     * @expectedExceptionMessage Communication error
     */
    public function testCommunicationExceptionHandling()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('isConnected')->will($this->returnValue(true));
        $connection->expects($this->once())->method('disconnect');

        $exception = $this->getException($connection, 'Communication error');

        CommunicationException::handle($exception);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a mocked connection instance.
     *
     * @param  mixed                     $parameters Connection parameters.
     * @return SingleConnectionInterface
     */
    protected function getMockedConnectionBase($parameters = null)
    {
        $builder = $this->getMockBuilder('Predis\Connection\AbstractConnection');

        if ($parameters === null) {
            $builder->disableOriginalConstructor();
        } elseif (!$parameters instanceof ConnectionParametersInterface) {
            $parameters = new ConnectionParameters($parameters);
        }

        return $builder->getMockForAbstractClass(array($parameters));
    }

    /**
     * Returns a connection exception instance.
     *
     * @param  Connection\SingleConnectionInterface $connection Connection instance.
     * @param  string                               $message    Exception message.
     * @param  int                                  $code       Exception code.
     * @param  \Exception                           $inner      Inner exception.
     * @return \Exception
     */
    protected function getException(SingleConnectionInterface $connection, $message, $code = 0, \Exception $inner = null)
    {
        $arguments = array($connection, $message, $code, $inner);
        $mock = $this->getMockForAbstractClass('Predis\CommunicationException', $arguments);

        return $mock;
    }
}

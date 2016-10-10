<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\PubSub;

use PredisTestCase;
use Predis\Client;

/**
 * @group realm-pubsub
 */
class DispatcherLoopTest extends PredisTestCase
{
    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testDispatcherLoopAgainstRedisServer()
    {
        $parameters = array(
            'host' => REDIS_SERVER_HOST,
            'port' => REDIS_SERVER_PORT,
            'database' => REDIS_SERVER_DBNUM,
            // Prevents suite from handing on broken test
            'read_write_timeout' => 2,
        );

        $options = array('profile' => REDIS_SERVER_VERSION);

        $producer = new Client($parameters, $options);
        $producer->connect();

        $consumer = new Client($parameters, $options);
        $consumer->connect();

        $dispatcher = new DispatcherLoop($consumer);

        $function01 = $this->getMock('stdClass', array('__invoke'));
        $function01->expects($this->exactly(2))
                   ->method('__invoke')
                   ->with($this->logicalOr(
                       $this->equalTo('01:argument'),
                       $this->equalTo('01:quit')
                   ))
                   ->will($this->returnCallback(function ($arg) use ($dispatcher) {
                       if ($arg === '01:quit') {
                           $dispatcher->stop();
                       }
                   }));

        $function02 = $this->getMock('stdClass', array('__invoke'));
        $function02->expects($this->once())
                   ->method('__invoke')
                   ->with('02:argument');

        $function03 = $this->getMock('stdClass', array('__invoke'));
        $function03->expects($this->never())
                   ->method('__invoke');

        $dispatcher->attachCallback('function:01', $function01);
        $dispatcher->attachCallback('function:02', $function02);
        $dispatcher->attachCallback('function:03', $function03);

        $producer->publish('function:01', '01:argument');
        $producer->publish('function:02', '02:argument');
        $producer->publish('function:01', '01:quit');

        $dispatcher->run();

        $this->assertTrue($consumer->ping());
    }

    /**
     * @group connected
     */
    public function testDispatcherLoopAgainstRedisServerWithPrefix()
    {
        $parameters = array(
            'host' => REDIS_SERVER_HOST,
            'port' => REDIS_SERVER_PORT,
            'database' => REDIS_SERVER_DBNUM,
            // Prevents suite from handing on broken test
            'read_write_timeout' => 2,
        );

        $options = array('profile' => REDIS_SERVER_VERSION);

        $producerNonPfx = new Client($parameters, $options);
        $producerNonPfx->connect();

        $producerPfx = new Client($parameters, $options + array('prefix' => 'foobar'));
        $producerPfx->connect();

        $consumer = new Client($parameters, $options + array('prefix' => 'foobar'));
        $dispatcher = new DispatcherLoop($consumer);

        $callback = $this->getMock('stdClass', array('__invoke'));
        $callback->expects($this->exactly(1))
                 ->method('__invoke')
                 ->with($this->equalTo('arg:prefixed'))
                 ->will($this->returnCallback(function ($arg) use ($dispatcher) {
                     $dispatcher->stop();
                 }));

        $dispatcher->attachCallback('callback', $callback);

        $producerNonPfx->publish('callback', 'arg:non-prefixed');
        $producerPfx->publish('callback', 'arg:prefixed');

        $dispatcher->run();

        $this->assertTrue($consumer->ping());
    }
}

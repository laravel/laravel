<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster;

use PredisTestCase;
use Predis\Profile\ServerProfile;

/**
 *
 */
class RedisClusterHashStrategyTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testSupportsKeyTags()
    {
        $strategy = $this->getHashStrategy();

        $this->assertSame(44950, $strategy->getKeyHash('{foo}'));
        $this->assertSame(44950, $strategy->getKeyHash('{foo}:bar'));
        $this->assertSame(44950, $strategy->getKeyHash('{foo}:baz'));
        $this->assertSame(44950, $strategy->getKeyHash('bar:{foo}:baz'));
        $this->assertSame(44950, $strategy->getKeyHash('bar:{foo}:{baz}'));

        $this->assertSame(44950, $strategy->getKeyHash('bar:{foo}:baz{}'));
        $this->assertSame(9415,  $strategy->getKeyHash('{}bar:{foo}:baz'));

        $this->assertSame(0,     $strategy->getKeyHash(''));
        $this->assertSame(31641, $strategy->getKeyHash('{}'));
    }

    /**
     * @group disconnected
     */
    public function testSupportedCommands()
    {
        $strategy = $this->getHashStrategy();

        $this->assertSame($this->getExpectedCommands(), $strategy->getSupportedCommands());
    }

    /**
     * @group disconnected
     */
    public function testReturnsNullOnUnsupportedCommand()
    {
        $strategy = $this->getHashStrategy();
        $command = ServerProfile::getDevelopment()->createCommand('ping');

        $this->assertNull($strategy->getHash($command));
    }

    /**
     * @group disconnected
     */
    public function testFirstKeyCommands()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key');

        foreach ($this->getExpectedCommands('keys-first') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNotNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testAllKeysCommandsWithOneKey()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key');

        foreach ($this->getExpectedCommands('keys-all') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNotNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testAllKeysCommandsWithMoreKeys()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key1', 'key2');

        foreach ($this->getExpectedCommands('keys-all') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testInterleavedKeysCommandsWithOneKey()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key:1', 'value1');

        foreach ($this->getExpectedCommands('keys-interleaved') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNotNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testInterleavedKeysCommandsWithMoreKeys()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key:1', 'value1', 'key:2', 'value2');

        foreach ($this->getExpectedCommands('keys-interleaved') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testKeysForBlockingListCommandsWithOneKey()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key:1', 10);

        foreach ($this->getExpectedCommands('keys-blockinglist') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNotNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testKeysForBlockingListCommandsWithMoreKeys()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('key:1', 'key:2', 10);

        foreach ($this->getExpectedCommands('keys-blockinglist') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testKeysForScriptCommand()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();
        $arguments = array('%SCRIPT%', 1, 'key:1', 'value1');

        foreach ($this->getExpectedCommands('keys-script') as $commandID) {
            $command = $profile->createCommand($commandID, $arguments);
            $this->assertNotNull($strategy->getHash($command), $commandID);
        }
    }

    /**
     * @group disconnected
     */
    public function testKeysForScriptedCommand()
    {
        $strategy = $this->getHashStrategy();
        $arguments = array('key:1', 'value1');

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript', 'getKeysCount'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue('return true'));
        $command->expects($this->exactly(2))
                ->method('getKeysCount')
                ->will($this->returnValue(1));
        $command->setArguments($arguments);

        $this->assertNotNull($strategy->getHash($command), "Scripted Command [{$command->getId()}]");
    }

    /**
     * @group disconnected
     */
    public function testUnsettingCommandHandler()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();

        $strategy->setCommandHandler('set');
        $strategy->setCommandHandler('get', null);

        $command = $profile->createCommand('set', array('key', 'value'));
        $this->assertNull($strategy->getHash($command));

        $command = $profile->createCommand('get', array('key'));
        $this->assertNull($strategy->getHash($command));
    }

    /**
     * @group disconnected
     */
    public function testSettingCustomCommandHandler()
    {
        $strategy = $this->getHashStrategy();
        $profile = ServerProfile::getDevelopment();

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($this->isInstanceOf('Predis\Command\CommandInterface'))
                 ->will($this->returnValue('key'));

        $strategy->setCommandHandler('get', $callable);

        $command = $profile->createCommand('get', array('key'));
        $this->assertNotNull($strategy->getHash($command));
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Creates the default hash strategy object.
     *
     * @return CommandHashStrategyInterface
     */
    protected function getHashStrategy()
    {
        $strategy = new RedisClusterHashStrategy();

        return $strategy;
    }

    /**
     * Returns the list of expected supported commands.
     *
     * @param  string $type Optional type of command (based on its keys)
     * @return array
     */
    protected function getExpectedCommands($type = null)
    {
        $commands = array(
            /* commands operating on the key space */
            'EXISTS'                => 'keys-first',
            'DEL'                   => 'keys-all',
            'TYPE'                  => 'keys-first',
            'EXPIRE'                => 'keys-first',
            'EXPIREAT'              => 'keys-first',
            'PERSIST'               => 'keys-first',
            'PEXPIRE'               => 'keys-first',
            'PEXPIREAT'             => 'keys-first',
            'TTL'                   => 'keys-first',
            'PTTL'                  => 'keys-first',
            'SORT'                  => 'keys-first', // TODO
            'DUMP'                  => 'keys-first',
            'RESTORE'               => 'keys-first',

            /* commands operating on string values */
            'APPEND'                => 'keys-first',
            'DECR'                  => 'keys-first',
            'DECRBY'                => 'keys-first',
            'GET'                   => 'keys-first',
            'GETBIT'                => 'keys-first',
            'MGET'                  => 'keys-all',
            'SET'                   => 'keys-first',
            'GETRANGE'              => 'keys-first',
            'GETSET'                => 'keys-first',
            'INCR'                  => 'keys-first',
            'INCRBY'                => 'keys-first',
            'INCRBYFLOAT'           => 'keys-first',
            'SETBIT'                => 'keys-first',
            'SETEX'                 => 'keys-first',
            'MSET'                  => 'keys-interleaved',
            'MSETNX'                => 'keys-interleaved',
            'SETNX'                 => 'keys-first',
            'SETRANGE'              => 'keys-first',
            'STRLEN'                => 'keys-first',
            'SUBSTR'                => 'keys-first',
            'BITOP'                 => 'keys-bitop',
            'BITCOUNT'              => 'keys-first',

            /* commands operating on lists */
            'LINSERT'               => 'keys-first',
            'LINDEX'                => 'keys-first',
            'LLEN'                  => 'keys-first',
            'LPOP'                  => 'keys-first',
            'RPOP'                  => 'keys-first',
            'RPOPLPUSH'             => 'keys-all',
            'BLPOP'                 => 'keys-blockinglist',
            'BRPOP'                 => 'keys-blockinglist',
            'BRPOPLPUSH'            => 'keys-blockinglist',
            'LPUSH'                 => 'keys-first',
            'LPUSHX'                => 'keys-first',
            'RPUSH'                 => 'keys-first',
            'RPUSHX'                => 'keys-first',
            'LRANGE'                => 'keys-first',
            'LREM'                  => 'keys-first',
            'LSET'                  => 'keys-first',
            'LTRIM'                 => 'keys-first',

            /* commands operating on sets */
            'SADD'                  => 'keys-first',
            'SCARD'                 => 'keys-first',
            'SDIFF'                 => 'keys-all',
            'SDIFFSTORE'            => 'keys-all',
            'SINTER'                => 'keys-all',
            'SINTERSTORE'           => 'keys-all',
            'SUNION'                => 'keys-all',
            'SUNIONSTORE'           => 'keys-all',
            'SISMEMBER'             => 'keys-first',
            'SMEMBERS'              => 'keys-first',
            'SSCAN'                 => 'keys-first',
            'SPOP'                  => 'keys-first',
            'SRANDMEMBER'           => 'keys-first',
            'SREM'                  => 'keys-first',

            /* commands operating on sorted sets */
            'ZADD'                  => 'keys-first',
            'ZCARD'                 => 'keys-first',
            'ZCOUNT'                => 'keys-first',
            'ZINCRBY'               => 'keys-first',
            'ZINTERSTORE'           => 'keys-zaggregated',
            'ZRANGE'                => 'keys-first',
            'ZRANGEBYSCORE'         => 'keys-first',
            'ZRANK'                 => 'keys-first',
            'ZREM'                  => 'keys-first',
            'ZREMRANGEBYRANK'       => 'keys-first',
            'ZREMRANGEBYSCORE'      => 'keys-first',
            'ZREVRANGE'             => 'keys-first',
            'ZREVRANGEBYSCORE'      => 'keys-first',
            'ZREVRANK'              => 'keys-first',
            'ZSCORE'                => 'keys-first',
            'ZUNIONSTORE'           => 'keys-zaggregated',
            'ZSCAN'                 => 'keys-first',
            'ZLEXCOUNT'             => 'keys-first',
            'ZRANGEBYLEX'           => 'keys-first',
            'ZREMRANGEBYLEX'        => 'keys-first',

            /* commands operating on hashes */
            'HDEL'                  => 'keys-first',
            'HEXISTS'               => 'keys-first',
            'HGET'                  => 'keys-first',
            'HGETALL'               => 'keys-first',
            'HMGET'                 => 'keys-first',
            'HMSET'                 => 'keys-first',
            'HINCRBY'               => 'keys-first',
            'HINCRBYFLOAT'          => 'keys-first',
            'HKEYS'                 => 'keys-first',
            'HLEN'                  => 'keys-first',
            'HSET'                  => 'keys-first',
            'HSETNX'                => 'keys-first',
            'HVALS'                 => 'keys-first',
            'HSCAN'                 => 'keys-first',

            /* commands operating on HyperLogLog */
            'PFADD'                 => 'keys-first',
            'PFCOUNT'               => 'keys-all',
            'PFMERGE'               => 'keys-all',

            /* scripting */
            'EVAL'                  => 'keys-script',
            'EVALSHA'               => 'keys-script',
        );

        if (isset($type)) {
            $commands = array_filter($commands, function ($expectedType) use ($type) {
                return $expectedType === $type;
            });
        }

        return array_keys($commands);
    }
}

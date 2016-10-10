<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Replication;

use PredisTestCase;
use Predis\Profile\ServerProfile;

/**
 *
 */
class ReplicationStrategyTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testReadCommands()
    {
        $profile = ServerProfile::getDevelopment();
        $strategy = new ReplicationStrategy();

        foreach ($this->getExpectedCommands('read') as $commandId) {
            $command = $profile->createCommand($commandId);
            $this->assertTrue($strategy->isReadOperation($command));
        }
    }

    /**
     * @group disconnected
     */
    public function testWriteCommands()
    {
        $profile = ServerProfile::getDevelopment();
        $strategy = new ReplicationStrategy();

        foreach ($this->getExpectedCommands('write') as $commandId) {
            $command = $profile->createCommand($commandId);
            $this->assertFalse($strategy->isReadOperation($command), $commandId);
        }
    }

    /**
     * @group disconnected
     */
    public function testDisallowedCommands()
    {
        $profile = ServerProfile::getDevelopment();
        $strategy = new ReplicationStrategy();

        foreach ($this->getExpectedCommands('disallowed') as $commandId) {
            $command = $profile->createCommand($commandId);
            $this->assertTrue($strategy->isDisallowedOperation($command), $commandId);
        }
    }

    /**
     * @group disconnected
     */
    public function testSortCommand()
    {
        $profile = ServerProfile::getDevelopment();
        $strategy = new ReplicationStrategy();

        $cmdReadSort = $profile->createCommand('SORT', array('key:list'));
        $this->assertTrue($strategy->isReadOperation($cmdReadSort), 'SORT [read-only]');

        $cmdWriteSort = $profile->createCommand('SORT', array('key:list', array('store' => 'key:stored')));
        $this->assertFalse($strategy->isReadOperation($cmdWriteSort), 'SORT [write with STORE]');
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The command INFO is not allowed in replication mode
     */
    public function testUsingDisallowedCommandThrowsException()
    {
        $profile = ServerProfile::getDevelopment();
        $strategy = new ReplicationStrategy();

        $command = $profile->createCommand('INFO');
        $strategy->isReadOperation($command);
    }

    /**
     * @group disconnected
     */
    public function testDefaultIsWriteOperation()
    {
        $strategy = new ReplicationStrategy();

        $command = $this->getMock('Predis\Command\CommandInterface');
        $command->expects($this->any())
                ->method('getId')
                ->will($this->returnValue('CMDTEST'));

        $this->assertFalse($strategy->isReadOperation($command));
    }

    /**
     * @group disconnected
     */
    public function testCanSetCommandAsReadOperation()
    {
        $strategy = new ReplicationStrategy();

        $command = $this->getMock('Predis\Command\CommandInterface');
        $command->expects($this->any())
                ->method('getId')
                ->will($this->returnValue('CMDTEST'));

        $strategy->setCommandReadOnly('CMDTEST', true);
        $this->assertTrue($strategy->isReadOperation($command));
    }

    /**
     * @group disconnected
     */
    public function testCanSetCommandAsWriteOperation()
    {
        $strategy = new ReplicationStrategy();

        $command = $this->getMock('Predis\Command\CommandInterface');
        $command->expects($this->any())
                ->method('getId')
                ->will($this->returnValue('CMDTEST'));

        $strategy->setCommandReadOnly('CMDTEST', false);
        $this->assertFalse($strategy->isReadOperation($command));

        $strategy->setCommandReadOnly('GET', false);
        $this->assertFalse($strategy->isReadOperation($command));
    }

    /**
     * @group disconnected
     */
    public function testCanUseCallableToCheckCommand()
    {
        $strategy = new ReplicationStrategy();
        $profile = ServerProfile::getDevelopment();

        $strategy->setCommandReadOnly('SET', function ($command) {
            return $command->getArgument(1) === true;
        });

        $command = $profile->createCommand('SET', array('trigger', false));
        $this->assertFalse($strategy->isReadOperation($command));

        $command = $profile->createCommand('SET', array('trigger', true));
        $this->assertTrue($strategy->isReadOperation($command));
    }

    /**
     * @group disconnected
     */
    public function testSetLuaScriptAsReadOperation()
    {
        $strategy = new ReplicationStrategy();
        $profile = ServerProfile::getDevelopment();

        $writeScript = 'redis.call("set", "foo", "bar")';
        $readScript = 'return true';

        $strategy->setScriptReadOnly($readScript, true);

        $cmdEval = $profile->createCommand('EVAL', array($writeScript));
        $cmdEvalSHA = $profile->createCommand('EVALSHA', array(sha1($writeScript)));
        $this->assertFalse($strategy->isReadOperation($cmdEval));
        $this->assertFalse($strategy->isReadOperation($cmdEvalSHA));

        $cmdEval = $profile->createCommand('EVAL', array($readScript));
        $cmdEvalSHA = $profile->createCommand('EVALSHA', array(sha1($readScript)));
        $this->assertTrue($strategy->isReadOperation($cmdEval));
        $this->assertTrue($strategy->isReadOperation($cmdEvalSHA));
    }

    /**
     * @group disconnected
     */
    public function testSetLuaScriptAsReadOperationWorksWithScriptedCommand()
    {
        $strategy = new ReplicationStrategy();

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript'));
        $command->expects($this->any())
                ->method('getScript')
                ->will($this->returnValue($script = 'return true'));

        $strategy->setScriptReadOnly($script, function ($command) {
            return $command->getArgument(2) === true;
        });

        $command->setArguments(array(false));
        $this->assertFalse($strategy->isReadOperation($command));

        $command->setArguments(array(true));
        $this->assertTrue($strategy->isReadOperation($command));
    }

    /**
     * @group disconnected
     */
    public function testSetLuaScriptAsReadOperationWorksWithScriptedCommandAndCallableCheck()
    {
        $strategy = new ReplicationStrategy();

        $command = $this->getMock('Predis\Command\ScriptedCommand', array('getScript'));
        $command->expects($this->any())
                ->method('getScript')
                ->will($this->returnValue($script = 'return true'));

        $command->setArguments(array('trigger', false));

        $strategy->setScriptReadOnly($script, true);

        $this->assertTrue($strategy->isReadOperation($command));
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns the list of expected supported commands.
     *
     * @param  string $type Optional type of command (based on its keys)
     * @return array
     */
    protected function getExpectedCommands($type = null)
    {
        $commands = array(
            /* commands operating on the connection */
            'AUTH'                  => 'read',
            'SELECT'                => 'read',
            'ECHO'                  => 'read',
            'QUIT'                  => 'read',
            'OBJECT'                => 'read',
            'BITCOUNT'              => 'read',
            'TIME'                  => 'read',
            'SHUTDOWN'              => 'disallowed',
            'INFO'                  => 'disallowed',
            'DBSIZE'                => 'disallowed',
            'LASTSAVE'              => 'disallowed',
            'CONFIG'                => 'disallowed',
            'MONITOR'               => 'disallowed',
            'SLAVEOF'               => 'disallowed',
            'SAVE'                  => 'disallowed',
            'BGSAVE'                => 'disallowed',
            'BGREWRITEAOF'          => 'disallowed',
            'SLOWLOG'               => 'disallowed',

            /* commands operating on the key space */
            'EXISTS'                => 'read',
            'DEL'                   => 'write',
            'TYPE'                  => 'read',
            'EXPIRE'                => 'write',
            'EXPIREAT'              => 'write',
            'PERSIST'               => 'write',
            'PEXPIRE'               => 'write',
            'PEXPIREAT'             => 'write',
            'TTL'                   => 'read',
            'PTTL'                  => 'write',
            'SORT'                  => 'variable',
            'KEYS'                  => 'read',
            'SCAN'                  => 'read',
            'RANDOMKEY'             => 'read',

            /* commands operating on string values */
            'APPEND'                => 'write',
            'DECR'                  => 'write',
            'DECRBY'                => 'write',
            'GET'                   => 'read',
            'GETBIT'                => 'read',
            'MGET'                  => 'read',
            'SET'                   => 'write',
            'GETRANGE'              => 'read',
            'GETSET'                => 'write',
            'INCR'                  => 'write',
            'INCRBY'                => 'write',
            'INCRBYFLOAT'           => 'write',
            'SETBIT'                => 'write',
            'SETEX'                 => 'write',
            'MSET'                  => 'write',
            'MSETNX'                => 'write',
            'SETNX'                 => 'write',
            'SETRANGE'              => 'write',
            'STRLEN'                => 'read',
            'SUBSTR'                => 'read',

            /* commands operating on lists */
            'LINSERT'               => 'write',
            'LINDEX'                => 'read',
            'LLEN'                  => 'read',
            'LPOP'                  => 'write',
            'RPOP'                  => 'write',
            'BLPOP'                 => 'write',
            'BRPOP'                 => 'write',
            'LPUSH'                 => 'write',
            'LPUSHX'                => 'write',
            'RPUSH'                 => 'write',
            'RPUSHX'                => 'write',
            'LRANGE'                => 'read',
            'LREM'                  => 'write',
            'LSET'                  => 'write',
            'LTRIM'                 => 'write',

            /* commands operating on sets */
            'SADD'                  => 'write',
            'SCARD'                 => 'read',
            'SISMEMBER'             => 'read',
            'SMEMBERS'              => 'read',
            'SSCAN'                 => 'read',
            'SRANDMEMBER'           => 'read',
            'SPOP'                  => 'write',
            'SREM'                  => 'write',
            'SINTER'                => 'read',
            'SUNION'                => 'read',
            'SDIFF'                 => 'read',

            /* commands operating on sorted sets */
            'ZADD'                  => 'write',
            'ZCARD'                 => 'read',
            'ZCOUNT'                => 'read',
            'ZINCRBY'               => 'write',
            'ZRANGE'                => 'read',
            'ZRANGEBYSCORE'         => 'read',
            'ZRANK'                 => 'read',
            'ZREM'                  => 'write',
            'ZREMRANGEBYRANK'       => 'write',
            'ZREMRANGEBYSCORE'      => 'write',
            'ZREVRANGE'             => 'read',
            'ZREVRANGEBYSCORE'      => 'read',
            'ZREVRANK'              => 'read',
            'ZSCORE'                => 'read',
            'ZSCAN'                 => 'read',
            'ZLEXCOUNT'             => 'read',
            'ZRANGEBYLEX'           => 'read',
            'ZREMRANGEBYLEX'        => 'write',

            /* commands operating on hashes */
            'HDEL'                  => 'write',
            'HEXISTS'               => 'read',
            'HGET'                  => 'read',
            'HGETALL'               => 'read',
            'HMGET'                 => 'read',
            'HINCRBY'               => 'write',
            'HINCRBYFLOAT'          => 'write',
            'HKEYS'                 => 'read',
            'HLEN'                  => 'read',
            'HSET'                  => 'write',
            'HSETNX'                => 'write',
            'HVALS'                 => 'read',
            'HSCAN'                 => 'read',

            /* commands operating on HyperLogLog */
            'PFADD'                 => 'write',
            'PFMERGE'               => 'write',
            'PFCOUNT'               => 'read',

            /* scripting */
            'EVAL'                  => 'write',
            'EVALSHA'               => 'write',
        );

        if (isset($type)) {
            $commands = array_filter($commands, function ($expectedType) use ($type) {
                return $expectedType === $type;
            });
        }

        return array_keys($commands);
    }
}

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

use Predis\NotSupportedException;
use Predis\Command\CommandInterface;

/**
 * Defines a strategy for master/reply replication.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ReplicationStrategy
{
    protected $disallowed;
    protected $readonly;
    protected $readonlySHA1;

    /**
     *
     */
    public function __construct()
    {
        $this->disallowed = $this->getDisallowedOperations();
        $this->readonly = $this->getReadOnlyOperations();
        $this->readonlySHA1 = array();
    }

    /**
     * Returns if the specified command performs a read-only operation
     * against a key stored on Redis.
     *
     * @param  CommandInterface $command Instance of Redis command.
     * @return bool
     */
    public function isReadOperation(CommandInterface $command)
    {
        if (isset($this->disallowed[$id = $command->getId()])) {
            throw new NotSupportedException("The command $id is not allowed in replication mode");
        }

        if (isset($this->readonly[$id])) {
            if (true === $readonly = $this->readonly[$id]) {
                return true;
            }

            return call_user_func($readonly, $command);
        }

        if (($eval = $id === 'EVAL') || $id === 'EVALSHA') {
            $sha1 = $eval ? sha1($command->getArgument(0)) : $command->getArgument(0);

            if (isset($this->readonlySHA1[$sha1])) {
                if (true === $readonly = $this->readonlySHA1[$sha1]) {
                    return true;
                }

                return call_user_func($readonly, $command);
            }
        }

        return false;
    }

    /**
     * Returns if the specified command is disallowed in a master/slave
     * replication context.
     *
     * @param  CommandInterface $command Instance of Redis command.
     * @return bool
     */
    public function isDisallowedOperation(CommandInterface $command)
    {
        return isset($this->disallowed[$command->getId()]);
    }

    /**
     * Checks if a SORT command is a readable operation by parsing the arguments
     * array of the specified commad instance.
     *
     * @param  CommandInterface $command Instance of Redis command.
     * @return bool
     */
    protected function isSortReadOnly(CommandInterface $command)
    {
        $arguments = $command->getArguments();

        return ($c = count($arguments)) === 1 ? true : $arguments[$c - 2] !== 'STORE';
    }

    /**
     * Marks a command as a read-only operation. When the behaviour of a
     * command can be decided only at runtime depending on its arguments,
     * a callable object can be provided to dynamically check if the passed
     * instance of a command performs write operations or not.
     *
     * @param string $commandID ID of the command.
     * @param mixed  $readonly  A boolean or a callable object.
     */
    public function setCommandReadOnly($commandID, $readonly = true)
    {
        $commandID = strtoupper($commandID);

        if ($readonly) {
            $this->readonly[$commandID] = $readonly;
        } else {
            unset($this->readonly[$commandID]);
        }
    }

    /**
     * Marks a Lua script for EVAL and EVALSHA as a read-only operation. When
     * the behaviour of a script can be decided only at runtime depending on
     * its arguments, a callable object can be provided to dynamically check
     * if the passed instance of EVAL or EVALSHA performs write operations or
     * not.
     *
     * @param string $script   Body of the Lua script.
     * @param mixed  $readonly A boolean or a callable object.
     */
    public function setScriptReadOnly($script, $readonly = true)
    {
        $sha1 = sha1($script);

        if ($readonly) {
            $this->readonlySHA1[$sha1] = $readonly;
        } else {
            unset($this->readonlySHA1[$sha1]);
        }
    }

    /**
     * Returns the default list of disallowed commands.
     *
     * @return array
     */
    protected function getDisallowedOperations()
    {
        return array(
            'SHUTDOWN'          => true,
            'INFO'              => true,
            'DBSIZE'            => true,
            'LASTSAVE'          => true,
            'CONFIG'            => true,
            'MONITOR'           => true,
            'SLAVEOF'           => true,
            'SAVE'              => true,
            'BGSAVE'            => true,
            'BGREWRITEAOF'      => true,
            'SLOWLOG'           => true,
        );
    }

    /**
     * Returns the default list of commands performing read-only operations.
     *
     * @return array
     */
    protected function getReadOnlyOperations()
    {
        return array(
            'EXISTS'            => true,
            'TYPE'              => true,
            'KEYS'              => true,
            'SCAN'              => true,
            'RANDOMKEY'         => true,
            'TTL'               => true,
            'GET'               => true,
            'MGET'              => true,
            'SUBSTR'            => true,
            'STRLEN'            => true,
            'GETRANGE'          => true,
            'GETBIT'            => true,
            'LLEN'              => true,
            'LRANGE'            => true,
            'LINDEX'            => true,
            'SCARD'             => true,
            'SISMEMBER'         => true,
            'SINTER'            => true,
            'SUNION'            => true,
            'SDIFF'             => true,
            'SMEMBERS'          => true,
            'SSCAN'             => true,
            'SRANDMEMBER'       => true,
            'ZRANGE'            => true,
            'ZREVRANGE'         => true,
            'ZRANGEBYSCORE'     => true,
            'ZREVRANGEBYSCORE'  => true,
            'ZCARD'             => true,
            'ZSCORE'            => true,
            'ZCOUNT'            => true,
            'ZRANK'             => true,
            'ZREVRANK'          => true,
            'ZSCAN'             => true,
            'ZLEXCOUNT'         => true,
            'ZRANGEBYLEX'       => true,
            'HGET'              => true,
            'HMGET'             => true,
            'HEXISTS'           => true,
            'HLEN'              => true,
            'HKEYS'             => true,
            'HVALS'             => true,
            'HGETALL'           => true,
            'HSCAN'             => true,
            'PING'              => true,
            'AUTH'              => true,
            'SELECT'            => true,
            'ECHO'              => true,
            'QUIT'              => true,
            'OBJECT'            => true,
            'BITCOUNT'          => true,
            'TIME'              => true,
            'PFCOUNT'           => true,
            'SORT'              => array($this, 'isSortReadOnly'),
        );
    }
}

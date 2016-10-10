<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Profile;

/**
 *
 */
class ServerVersion12Test extends PredisProfileTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getProfile($version = null)
    {
        return new ServerVersion12();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedVersion()
    {
        return '1.2';
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedCommands()
    {
        return array(
            0 => 'exists',
            1 => 'del',
            2 => 'type',
            3 => 'keys',
            4 => 'randomkey',
            5 => 'rename',
            6 => 'renamenx',
            7 => 'expire',
            8 => 'expireat',
            9 => 'ttl',
            10 => 'move',
            11 => 'sort',
            12 => 'set',
            13 => 'setnx',
            14 => 'mset',
            15 => 'msetnx',
            16 => 'get',
            17 => 'mget',
            18 => 'getset',
            19 => 'incr',
            20 => 'incrby',
            21 => 'decr',
            22 => 'decrby',
            23 => 'rpush',
            24 => 'lpush',
            25 => 'llen',
            26 => 'lrange',
            27 => 'ltrim',
            28 => 'lindex',
            29 => 'lset',
            30 => 'lrem',
            31 => 'lpop',
            32 => 'rpop',
            33 => 'rpoplpush',
            34 => 'sadd',
            35 => 'srem',
            36 => 'spop',
            37 => 'smove',
            38 => 'scard',
            39 => 'sismember',
            40 => 'sinter',
            41 => 'sinterstore',
            42 => 'sunion',
            43 => 'sunionstore',
            44 => 'sdiff',
            45 => 'sdiffstore',
            46 => 'smembers',
            47 => 'srandmember',
            48 => 'zadd',
            49 => 'zincrby',
            50 => 'zrem',
            51 => 'zrange',
            52 => 'zrevrange',
            53 => 'zrangebyscore',
            54 => 'zcard',
            55 => 'zscore',
            56 => 'zremrangebyscore',
            57 => 'ping',
            58 => 'auth',
            59 => 'select',
            60 => 'echo',
            61 => 'quit',
            62 => 'info',
            63 => 'slaveof',
            64 => 'monitor',
            65 => 'dbsize',
            66 => 'flushdb',
            67 => 'flushall',
            68 => 'save',
            69 => 'bgsave',
            70 => 'lastsave',
            71 => 'shutdown',
            72 => 'bgrewriteaof',
        );
    }
}

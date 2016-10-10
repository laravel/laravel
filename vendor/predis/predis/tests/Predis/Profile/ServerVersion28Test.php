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
class ServerVersion28Test extends PredisProfileTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getProfile($version = null)
    {
        return new ServerVersion28();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedVersion()
    {
        return '2.8';
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
            12 => 'dump',
            13 => 'restore',
            14 => 'set',
            15 => 'setnx',
            16 => 'mset',
            17 => 'msetnx',
            18 => 'get',
            19 => 'mget',
            20 => 'getset',
            21 => 'incr',
            22 => 'incrby',
            23 => 'decr',
            24 => 'decrby',
            25 => 'rpush',
            26 => 'lpush',
            27 => 'llen',
            28 => 'lrange',
            29 => 'ltrim',
            30 => 'lindex',
            31 => 'lset',
            32 => 'lrem',
            33 => 'lpop',
            34 => 'rpop',
            35 => 'rpoplpush',
            36 => 'sadd',
            37 => 'srem',
            38 => 'spop',
            39 => 'smove',
            40 => 'scard',
            41 => 'sismember',
            42 => 'sinter',
            43 => 'sinterstore',
            44 => 'sunion',
            45 => 'sunionstore',
            46 => 'sdiff',
            47 => 'sdiffstore',
            48 => 'smembers',
            49 => 'srandmember',
            50 => 'zadd',
            51 => 'zincrby',
            52 => 'zrem',
            53 => 'zrange',
            54 => 'zrevrange',
            55 => 'zrangebyscore',
            56 => 'zcard',
            57 => 'zscore',
            58 => 'zremrangebyscore',
            59 => 'ping',
            60 => 'auth',
            61 => 'select',
            62 => 'echo',
            63 => 'quit',
            64 => 'info',
            65 => 'slaveof',
            66 => 'monitor',
            67 => 'dbsize',
            68 => 'flushdb',
            69 => 'flushall',
            70 => 'save',
            71 => 'bgsave',
            72 => 'lastsave',
            73 => 'shutdown',
            74 => 'bgrewriteaof',
            75 => 'setex',
            76 => 'append',
            77 => 'substr',
            78 => 'blpop',
            79 => 'brpop',
            80 => 'zunionstore',
            81 => 'zinterstore',
            82 => 'zcount',
            83 => 'zrank',
            84 => 'zrevrank',
            85 => 'zremrangebyrank',
            86 => 'hset',
            87 => 'hsetnx',
            88 => 'hmset',
            89 => 'hincrby',
            90 => 'hget',
            91 => 'hmget',
            92 => 'hdel',
            93 => 'hexists',
            94 => 'hlen',
            95 => 'hkeys',
            96 => 'hvals',
            97 => 'hgetall',
            98 => 'multi',
            99 => 'exec',
            100 => 'discard',
            101 => 'subscribe',
            102 => 'unsubscribe',
            103 => 'psubscribe',
            104 => 'punsubscribe',
            105 => 'publish',
            106 => 'config',
            107 => 'persist',
            108 => 'strlen',
            109 => 'setrange',
            110 => 'getrange',
            111 => 'setbit',
            112 => 'getbit',
            113 => 'rpushx',
            114 => 'lpushx',
            115 => 'linsert',
            116 => 'brpoplpush',
            117 => 'zrevrangebyscore',
            118 => 'watch',
            119 => 'unwatch',
            120 => 'object',
            121 => 'slowlog',
            122 => 'client',
            123 => 'pttl',
            124 => 'pexpire',
            125 => 'pexpireat',
            126 => 'psetex',
            127 => 'incrbyfloat',
            128 => 'bitop',
            129 => 'bitcount',
            130 => 'hincrbyfloat',
            131 => 'eval',
            132 => 'evalsha',
            133 => 'script',
            134 => 'time',
            135 => 'scan',
            136 => 'sscan',
            137 => 'zscan',
            138 => 'zlexcount',
            139 => 'zrangebylex',
            140 => 'zremrangebylex',
            141 => 'hscan',
            142 => 'pfadd',
            143 => 'pfcount',
            144 => 'pfmerge',
            145 => 'command',
        );
    }
}

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
 * Server profile for Redis v2.6.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion26 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.6';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Command\KeyExists',
            'del'                       => 'Predis\Command\KeyDelete',
            'type'                      => 'Predis\Command\KeyType',
            'keys'                      => 'Predis\Command\KeyKeys',
            'randomkey'                 => 'Predis\Command\KeyRandom',
            'rename'                    => 'Predis\Command\KeyRename',
            'renamenx'                  => 'Predis\Command\KeyRenamePreserve',
            'expire'                    => 'Predis\Command\KeyExpire',
            'expireat'                  => 'Predis\Command\KeyExpireAt',
            'ttl'                       => 'Predis\Command\KeyTimeToLive',
            'move'                      => 'Predis\Command\KeyMove',
            'sort'                      => 'Predis\Command\KeySort',
            'dump'                      => 'Predis\Command\KeyDump',
            'restore'                   => 'Predis\Command\KeyRestore',

            /* commands operating on string values */
            'set'                       => 'Predis\Command\StringSet',
            'setnx'                     => 'Predis\Command\StringSetPreserve',
            'mset'                      => 'Predis\Command\StringSetMultiple',
            'msetnx'                    => 'Predis\Command\StringSetMultiplePreserve',
            'get'                       => 'Predis\Command\StringGet',
            'mget'                      => 'Predis\Command\StringGetMultiple',
            'getset'                    => 'Predis\Command\StringGetSet',
            'incr'                      => 'Predis\Command\StringIncrement',
            'incrby'                    => 'Predis\Command\StringIncrementBy',
            'decr'                      => 'Predis\Command\StringDecrement',
            'decrby'                    => 'Predis\Command\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Command\ListPushTail',
            'lpush'                     => 'Predis\Command\ListPushHead',
            'llen'                      => 'Predis\Command\ListLength',
            'lrange'                    => 'Predis\Command\ListRange',
            'ltrim'                     => 'Predis\Command\ListTrim',
            'lindex'                    => 'Predis\Command\ListIndex',
            'lset'                      => 'Predis\Command\ListSet',
            'lrem'                      => 'Predis\Command\ListRemove',
            'lpop'                      => 'Predis\Command\ListPopFirst',
            'rpop'                      => 'Predis\Command\ListPopLast',
            'rpoplpush'                 => 'Predis\Command\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Command\SetAdd',
            'srem'                      => 'Predis\Command\SetRemove',
            'spop'                      => 'Predis\Command\SetPop',
            'smove'                     => 'Predis\Command\SetMove',
            'scard'                     => 'Predis\Command\SetCardinality',
            'sismember'                 => 'Predis\Command\SetIsMember',
            'sinter'                    => 'Predis\Command\SetIntersection',
            'sinterstore'               => 'Predis\Command\SetIntersectionStore',
            'sunion'                    => 'Predis\Command\SetUnion',
            'sunionstore'               => 'Predis\Command\SetUnionStore',
            'sdiff'                     => 'Predis\Command\SetDifference',
            'sdiffstore'                => 'Predis\Command\SetDifferenceStore',
            'smembers'                  => 'Predis\Command\SetMembers',
            'srandmember'               => 'Predis\Command\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Command\ZSetAdd',
            'zincrby'                   => 'Predis\Command\ZSetIncrementBy',
            'zrem'                      => 'Predis\Command\ZSetRemove',
            'zrange'                    => 'Predis\Command\ZSetRange',
            'zrevrange'                 => 'Predis\Command\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Command\ZSetRangeByScore',
            'zcard'                     => 'Predis\Command\ZSetCardinality',
            'zscore'                    => 'Predis\Command\ZSetScore',
            'zremrangebyscore'          => 'Predis\Command\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Command\ConnectionPing',
            'auth'                      => 'Predis\Command\ConnectionAuth',
            'select'                    => 'Predis\Command\ConnectionSelect',
            'echo'                      => 'Predis\Command\ConnectionEcho',
            'quit'                      => 'Predis\Command\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Command\ServerInfoV26x',
            'slaveof'                   => 'Predis\Command\ServerSlaveOf',
            'monitor'                   => 'Predis\Command\ServerMonitor',
            'dbsize'                    => 'Predis\Command\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Command\ServerFlushDatabase',
            'flushall'                  => 'Predis\Command\ServerFlushAll',
            'save'                      => 'Predis\Command\ServerSave',
            'bgsave'                    => 'Predis\Command\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Command\ServerLastSave',
            'shutdown'                  => 'Predis\Command\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Command\ServerBackgroundRewriteAOF',

            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => 'Predis\Command\StringSetExpire',
            'append'                    => 'Predis\Command\StringAppend',
            'substr'                    => 'Predis\Command\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => 'Predis\Command\ListPopFirstBlocking',
            'brpop'                     => 'Predis\Command\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => 'Predis\Command\ZSetUnionStore',
            'zinterstore'               => 'Predis\Command\ZSetIntersectionStore',
            'zcount'                    => 'Predis\Command\ZSetCount',
            'zrank'                     => 'Predis\Command\ZSetRank',
            'zrevrank'                  => 'Predis\Command\ZSetReverseRank',
            'zremrangebyrank'           => 'Predis\Command\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => 'Predis\Command\HashSet',
            'hsetnx'                    => 'Predis\Command\HashSetPreserve',
            'hmset'                     => 'Predis\Command\HashSetMultiple',
            'hincrby'                   => 'Predis\Command\HashIncrementBy',
            'hget'                      => 'Predis\Command\HashGet',
            'hmget'                     => 'Predis\Command\HashGetMultiple',
            'hdel'                      => 'Predis\Command\HashDelete',
            'hexists'                   => 'Predis\Command\HashExists',
            'hlen'                      => 'Predis\Command\HashLength',
            'hkeys'                     => 'Predis\Command\HashKeys',
            'hvals'                     => 'Predis\Command\HashValues',
            'hgetall'                   => 'Predis\Command\HashGetAll',

            /* transactions */
            'multi'                     => 'Predis\Command\TransactionMulti',
            'exec'                      => 'Predis\Command\TransactionExec',
            'discard'                   => 'Predis\Command\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => 'Predis\Command\PubSubSubscribe',
            'unsubscribe'               => 'Predis\Command\PubSubUnsubscribe',
            'psubscribe'                => 'Predis\Command\PubSubSubscribeByPattern',
            'punsubscribe'              => 'Predis\Command\PubSubUnsubscribeByPattern',
            'publish'                   => 'Predis\Command\PubSubPublish',

            /* remote server control commands */
            'config'                    => 'Predis\Command\ServerConfig',

            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'persist'                   => 'Predis\Command\KeyPersist',

            /* commands operating on string values */
            'strlen'                    => 'Predis\Command\StringStrlen',
            'setrange'                  => 'Predis\Command\StringSetRange',
            'getrange'                  => 'Predis\Command\StringGetRange',
            'setbit'                    => 'Predis\Command\StringSetBit',
            'getbit'                    => 'Predis\Command\StringGetBit',

            /* commands operating on lists */
            'rpushx'                    => 'Predis\Command\ListPushTailX',
            'lpushx'                    => 'Predis\Command\ListPushHeadX',
            'linsert'                   => 'Predis\Command\ListInsert',
            'brpoplpush'                => 'Predis\Command\ListPopLastPushHeadBlocking',

            /* commands operating on sorted sets */
            'zrevrangebyscore'          => 'Predis\Command\ZSetReverseRangeByScore',

            /* transactions */
            'watch'                     => 'Predis\Command\TransactionWatch',
            'unwatch'                   => 'Predis\Command\TransactionUnwatch',

            /* remote server control commands */
            'object'                    => 'Predis\Command\ServerObject',
            'slowlog'                   => 'Predis\Command\ServerSlowlog',

            /* ---------------- Redis 2.4 ---------------- */

            /* remote server control commands */
            'client'                    => 'Predis\Command\ServerClient',

            /* ---------------- Redis 2.6 ---------------- */

            /* commands operating on the key space */
            'pttl'                      => 'Predis\Command\KeyPreciseTimeToLive',
            'pexpire'                   => 'Predis\Command\KeyPreciseExpire',
            'pexpireat'                 => 'Predis\Command\KeyPreciseExpireAt',

            /* commands operating on string values */
            'psetex'                    => 'Predis\Command\StringPreciseSetExpire',
            'incrbyfloat'               => 'Predis\Command\StringIncrementByFloat',
            'bitop'                     => 'Predis\Command\StringBitOp',
            'bitcount'                  => 'Predis\Command\StringBitCount',

            /* commands operating on hashes */
            'hincrbyfloat'              => 'Predis\Command\HashIncrementByFloat',

            /* scripting */
            'eval'                      => 'Predis\Command\ServerEval',
            'evalsha'                   => 'Predis\Command\ServerEvalSHA',
            'script'                    => 'Predis\Command\ServerScript',

            /* remote server control commands */
            'time'                      => 'Predis\Command\ServerTime',
        );
    }
}

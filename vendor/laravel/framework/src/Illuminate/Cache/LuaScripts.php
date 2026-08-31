<?php

namespace Illuminate\Cache;

class LuaScripts
{
    /**
     * Get the Lua script that sets a key only when it does not yet exist.
     *
     * KEYS[1] - The name of the key
     * ARGV[1] - The value of the key
     * ARGV[2] - The number of seconds the key should be valid
     *
     * @return string
     */
    public static function add()
    {
        return <<<'LUA'
return redis.call('exists',KEYS[1])<1 and redis.call('setex',KEYS[1],ARGV[2],ARGV[1])
LUA;
    }

    /**
     * Get the Lua script to atomically refresh a lock's expiration.
     *
     * KEYS[1] - The name of the lock
     * ARGV[1] - The owner key of the lock instance trying to refresh it
     * ARGV[2] - The number of seconds the lock should be valid
     *
     * @return string
     */
    public static function refreshLock()
    {
        return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    if tonumber(ARGV[2]) > 0 then
        return redis.call("expire",KEYS[1],ARGV[2])
    end

    redis.call("persist",KEYS[1])

    return 1
else
    return 0
end
LUA;
    }

    /**
     * Get the Lua script to atomically release a lock.
     *
     * KEYS[1] - The name of the lock
     * ARGV[1] - The owner key of the lock instance trying to release it
     *
     * @return string
     */
    public static function releaseLock()
    {
        return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
    }
}

<?php namespace Laravel\Cache\Drivers;

class Blackhole extends Driver
{
    public function forever()
    {
        return false;
    }

    public function forget($key)
    {
        return false;
    }

    public function put($key, $value, $minutes)
    {
        return;
    }

    public function get($key, $default = null)
    {
        return null;
    }

    public function has($key)
    {
        return false;
    }

    public function retrieve($key)
    {
        return false;
    }
}
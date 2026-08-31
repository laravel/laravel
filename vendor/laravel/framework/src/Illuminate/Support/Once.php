<?php

namespace Illuminate\Support;

use WeakMap;

class Once
{
    /**
     * The current globally used instance.
     *
     * @var static|null
     */
    protected static ?self $instance = null;

    /**
     * Indicates if the once instance is enabled.
     *
     * @var bool
     */
    protected static bool $enabled = true;

    /**
     * Create a new once instance.
     *
     * @param  \WeakMap<object, array<string, mixed>>  $values
     */
    protected function __construct(protected WeakMap $values)
    {
        //
    }

    /**
     * Create a new once instance.
     *
     * @return static
     */
    public static function instance()
    {
        return static::$instance ??= new static(new WeakMap);
    }

    /**
     * Get the value of the given onceable.
     *
     * @param  Onceable  $onceable
     * @return mixed
     */
    public function value(Onceable $onceable)
    {
        if (! static::$enabled) {
            return call_user_func($onceable->callable);
        }

        $object = $onceable->object ?: $this;

        $hash = $onceable->hash;

        if (! isset($this->values[$object])) {
            $this->values[$object] = [];
        }

        if (array_key_exists($hash, $this->values[$object])) {
            return $this->values[$object][$hash];
        }

        return $this->values[$object][$hash] = call_user_func($onceable->callable);
    }

    /**
     * Re-enable the once instance if it was disabled.
     *
     * @return void
     */
    public static function enable()
    {
        static::$enabled = true;
    }

    /**
     * Disable the once instance.
     *
     * @return void
     */
    public static function disable()
    {
        static::$enabled = false;
    }

    /**
     * Flush the once instance.
     *
     * @return void
     */
    public static function flush()
    {
        static::$instance = null;
    }
}

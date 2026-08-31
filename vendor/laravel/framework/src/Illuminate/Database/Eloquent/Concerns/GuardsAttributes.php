<?php

namespace Illuminate\Database\Eloquent\Concerns;

trait GuardsAttributes
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['*'];

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = false;

    /**
     * The actual columns that exist on the database and can be guarded.
     *
     * @var array<class-string,list<string>>
     */
    protected static $guardableColumns = [];

    /**
     * Get the fillable attributes for the model.
     *
     * @return array<string>
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array<string>  $fillable
     * @return $this
     */
    public function fillable(array $fillable)
    {
        $this->fillable = $fillable;

        return $this;
    }

    /**
     * Merge new fillable attributes with existing fillable attributes on the model.
     *
     * @param  array<string>  $fillable
     * @return $this
     */
    public function mergeFillable(array $fillable)
    {
        $this->fillable = array_values(array_unique(array_merge($this->fillable, $fillable)));

        return $this;
    }

    /**
     * Get the guarded attributes for the model.
     *
     * @return array<string>
     */
    public function getGuarded()
    {
        return self::$unguarded === true
            ? []
            : $this->guarded;
    }

    /**
     * Set the guarded attributes for the model.
     *
     * @param  array<string>  $guarded
     * @return $this
     */
    public function guard(array $guarded)
    {
        $this->guarded = $guarded;

        return $this;
    }

    /**
     * Merge new guarded attributes with existing guarded attributes on the model.
     *
     * @param  array<string>  $guarded
     * @return $this
     */
    public function mergeGuarded(array $guarded)
    {
        $this->guarded = array_values(array_unique(array_merge($this->guarded, $guarded)));

        return $this;
    }

    /**
     * Disable all mass assignable restrictions.
     *
     * @param  bool  $state
     * @return void
     */
    public static function unguard($state = true)
    {
        static::$unguarded = $state;
    }

    /**
     * Enable the mass assignment restrictions.
     *
     * @return void
     */
    public static function reguard()
    {
        static::$unguarded = false;
    }

    /**
     * Determine if the current state is "unguarded".
     *
     * @return bool
     */
    public static function isUnguarded()
    {
        return static::$unguarded;
    }

    /**
     * Run the given callable while being unguarded.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function unguarded(callable $callback)
    {
        if (static::$unguarded) {
            return $callback();
        }

        static::unguard();

        try {
            return $callback();
        } finally {
            static::reguard();
        }
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key)
    {
        if (static::$unguarded) {
            return true;
        }

        // If the key is in the "fillable" array, we can of course assume that it's
        // a fillable attribute. Otherwise, we will check the guarded array when
        // we need to determine if the attribute is black-listed on the model.
        if (in_array($key, $this->getFillable())) {
            return true;
        }

        // If the attribute is explicitly listed in the "guarded" array then we can
        // return false immediately. This means this attribute is definitely not
        // fillable and there is no point in going any further in this method.
        if ($this->isGuarded($key)) {
            return false;
        }

        return empty($this->getFillable()) &&
            ! str_contains($key, '.') &&
            ! str_starts_with($key, '_');
    }

    /**
     * Determine if the given key is guarded.
     *
     * @param  string  $key
     * @return bool
     */
    public function isGuarded($key)
    {
        if (empty($this->getGuarded())) {
            return false;
        }

        return $this->getGuarded() == ['*'] ||
               ! empty(preg_grep('/^'.preg_quote($key, '/').'$/i', $this->getGuarded())) ||
               ! $this->isGuardableColumn($key);
    }

    /**
     * Determine if the given column is a valid, guardable column.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isGuardableColumn($key)
    {
        if ($this->hasSetMutator($key) || $this->hasAttributeSetMutator($key) || $this->isClassCastable($key)) {
            return true;
        }

        if (! isset(static::$guardableColumns[get_class($this)])) {
            $columns = $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable());

            if (empty($columns)) {
                return true;
            }

            static::$guardableColumns[get_class($this)] = $columns;
        }

        return in_array($key, static::$guardableColumns[get_class($this)]);
    }

    /**
     * Determine if the model is totally guarded.
     *
     * @return bool
     */
    public function totallyGuarded()
    {
        return count($this->getFillable()) === 0 && $this->getGuarded() == ['*'];
    }

    /**
     * Get the fillable attributes of a given array.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function fillableFromArray(array $attributes)
    {
        if (count($this->getFillable()) > 0 && ! static::$unguarded) {
            return array_intersect_key($attributes, array_flip($this->getFillable()));
        }

        return $attributes;
    }
}

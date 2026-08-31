<?php

namespace Illuminate\Support;

class HigherOrderWhenProxy
{
    /**
     * The target being conditionally operated on.
     *
     * @var mixed
     */
    protected $target;

    /**
     * The condition for proxying.
     *
     * @var bool
     */
    protected $condition;

    /**
     * Indicates whether the proxy has a condition.
     *
     * @var bool
     */
    protected $hasCondition = false;

    /**
     * Determine whether the condition should be negated.
     *
     * @var bool
     */
    protected $negateConditionOnCapture;

    /**
     * Create a new proxy instance.
     *
     * @param  mixed  $target
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * Set the condition on the proxy.
     *
     * @param  bool  $condition
     * @return $this
     */
    public function condition($condition)
    {
        [$this->condition, $this->hasCondition] = [$condition, true];

        return $this;
    }

    /**
     * Indicate that the condition should be negated.
     *
     * @return $this
     */
    public function negateConditionOnCapture()
    {
        $this->negateConditionOnCapture = true;

        return $this;
    }

    /**
     * Proxy accessing an attribute onto the target.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (! $this->hasCondition) {
            $condition = $this->target->{$key};

            return $this->condition($this->negateConditionOnCapture ? ! $condition : $condition);
        }

        return $this->condition
            ? $this->target->{$key}
            : $this->target;
    }

    /**
     * Proxy a method call on the target.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! $this->hasCondition) {
            $condition = $this->target->{$method}(...$parameters);

            return $this->condition($this->negateConditionOnCapture ? ! $condition : $condition);
        }

        return $this->condition
            ? $this->target->{$method}(...$parameters)
            : $this->target;
    }
}

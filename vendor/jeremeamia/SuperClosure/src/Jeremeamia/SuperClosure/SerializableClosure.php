<?php

namespace Jeremeamia\SuperClosure;

/**
 * This class allows you to do the impossible - serialize closures! With the combined power of the nikic/php-parser
 * library, the Reflection API, and infamous eval, you can serialize a closure, unserialize it in a different PHP
 * process, and execute it. It's almost as cool as time travel!
 *
 * @copyright Jeremy Lindblom 2010-2013
 */
class SerializableClosure implements \Serializable
{
    /**
     * @var \Closure The closure being made serializable
     */
    protected $closure;

    /**
     * @var \ReflectionFunction The reflected closure
     */
    protected $reflection;

    /**
     * @var array The calculated state to serialize
     */
    protected $state;

    /**
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @return \ReflectionFunction
     */
    public function getReflection()
    {
        if (!$this->reflection) {
            $this->reflection = new \ReflectionFunction($this->closure);
        }

        return $this->reflection;
    }

    /**
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * Invokes the original closure
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->getReflection()->invokeArgs(func_get_args());
    }

    /**
     * Serialize the code and of context of the closure
     *
     * @return string
     */
    public function serialize()
    {
        if (!$this->state) {
            $this->createState();
        }

        return serialize($this->state);
    }

    /**
     * Unserializes the closure data and recreates the closure. Attempts to recreate the closure's context as well by
     * extracting the used variables into the scope. Variables names in this method are surrounded with underlines in
     * order to prevent collisions with the variables in the context. NOTE: There be dragons here! Both `eval` and
     * `extract` are used in this method
     *
     * @param string $__serialized__
     */
    public function unserialize($__serialized__)
    {
        // Unserialize the data we need to reconstruct the SuperClosure
        $this->state = unserialize($__serialized__);
        list($__code__, $__context__) = $this->state;

        // Simulate the original context the Closure was created in
        extract($__context__);

        // Evaluate the code to recreate the Closure
        eval("\$this->closure = {$__code__};");
    }

    /**
     * Uses the closure parser to fetch the closure's code and context
     */
    protected function createState()
    {
        $parser = new ClosureParser($this->getReflection());
        $this->state = array($parser->getCode());
        // Add the used variables (context) to the state, but wrap all closures with SerializableClosure
        $this->state[] = array_map(function ($var) {
            return ($var instanceof \Closure) ? new self($var) : $var;
        }, $parser->getUsedVariables());
    }
}

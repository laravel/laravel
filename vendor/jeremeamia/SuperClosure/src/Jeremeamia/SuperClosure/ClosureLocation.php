<?php

namespace Jeremeamia\SuperClosure;

/**
 * Simple object for storing the location information of a closure (e.g., file, class, etc.)
 *
 * @copyright Jeremy Lindblom 2010-2013
 */
class ClosureLocation
{
    /** @var string */
    protected $closureScopeClass;

    /** @var string */
    public $class;

    /** @var string */
    public $directory;

    /** @var string */
    public $file;

    /** @var string */
    public $function;

    /** @var string */
    public $line;

    /** @var string */
    public $method;

    /** @var string */
    public $namespace;

    /** @var string */
    public $trait;

    /**
     * Creates a ClosureLocation and seeds it with all the data that can be gleaned from the closure's reflection
     *
     * @param \ReflectionFunction $reflection The reflection of the closure that this ClosureLocation should represent
     *
     * @return ClosureLocation
     */
    public static function fromReflection(\ReflectionFunction $reflection)
    {
        $location = new self;
        $location->directory = dirname($reflection->getFileName());
        $location->file = $reflection->getFileName();
        $location->function = $reflection->getName();
        $location->line = $reflection->getStartLine();

        // @codeCoverageIgnoreStart
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $closureScopeClass = $reflection->getClosureScopeClass();
            $location->closureScopeClass = $closureScopeClass ? $closureScopeClass->getName() : null;
        }
        // @codeCoverageIgnoreEnd

        return $location;
    }

    public function finalize()
    {
        if ($this->class || $this->trait) {
            $class = $this->class ?: $this->trait;
            $this->method = "{$class}::{$this->function}";
        }

        if (!$this->class && $this->trait) {
            $this->class = $this->closureScopeClass;
        }
    }


}

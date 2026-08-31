<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

/**
 * The Shell execution context.
 *
 * This class encapsulates the current variables, most recent return value and
 * exception, and the current namespace.
 */
class Context
{
    private const SPECIAL_NAMES = ['_', '_e', '__out', '__psysh__', 'this'];

    // Include a very limited number of command-scope magic variable names.
    // This might be a bad idea, but future me can sort it out.
    private const COMMAND_SCOPE_NAMES = [
        '__function', '__method', '__class', '__namespace', '__file', '__line', '__dir',
    ];

    private array $scopeVariables = [];
    private array $commandScopeVariables = [];
    /** @var mixed */
    private $returnValue = null;
    private ?\Throwable $lastException = null;
    private ?string $lastStdout = null;
    private ?object $boundObject = null;
    private ?string $boundClass = null;

    /**
     * Get a context variable.
     *
     * @throws \InvalidArgumentException If the variable is not found in the current context
     *
     * @return mixed
     */
    public function get(string $name)
    {
        switch ($name) {
            case '_':
                return $this->returnValue;

            case '_e':
                if (isset($this->lastException)) {
                    return $this->lastException;
                }
                break;

            case '__out':
                if (isset($this->lastStdout)) {
                    return $this->lastStdout;
                }
                break;

            case 'this':
                if (isset($this->boundObject)) {
                    return $this->boundObject;
                }
                break;

            case '__function':
            case '__method':
            case '__class':
            case '__namespace':
            case '__file':
            case '__line':
            case '__dir':
                if (\array_key_exists($name, $this->commandScopeVariables)) {
                    return $this->commandScopeVariables[$name];
                }
                break;

            default:
                if (\array_key_exists($name, $this->scopeVariables)) {
                    return $this->scopeVariables[$name];
                }
                break;
        }

        throw new \InvalidArgumentException('Unknown variable: $'.$name);
    }

    /**
     * Get all defined variables.
     */
    public function getAll(): array
    {
        return \array_merge($this->scopeVariables, $this->getSpecialVariables());
    }

    /**
     * Get all defined magic variables: $_, $_e, $__out, $__class, $__file, etc.
     */
    public function getSpecialVariables(): array
    {
        $vars = [
            '_' => $this->returnValue,
        ];

        if (isset($this->lastException)) {
            $vars['_e'] = $this->lastException;
        }

        if (isset($this->lastStdout)) {
            $vars['__out'] = $this->lastStdout;
        }

        if (isset($this->boundObject)) {
            $vars['this'] = $this->boundObject;
        }

        return \array_merge($vars, $this->commandScopeVariables);
    }

    /**
     * Set all scope variables.
     *
     * This method does *not* set any of the magic variables: $_, $_e, $__out,
     * $__class, $__file, etc.
     */
    public function setAll(array $vars)
    {
        foreach (self::SPECIAL_NAMES as $key) {
            unset($vars[$key]);
        }

        foreach (self::COMMAND_SCOPE_NAMES as $key) {
            unset($vars[$key]);
        }

        $this->scopeVariables = $vars;
    }

    /**
     * Set the most recent return value.
     *
     * @param mixed $value
     */
    public function setReturnValue($value)
    {
        $this->returnValue = $value;
    }

    /**
     * Get the most recent return value.
     *
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * Set the most recent Exception or Error.
     *
     * @param \Throwable $e
     */
    public function setLastException(\Throwable $e)
    {
        $this->lastException = $e;
    }

    /**
     * Get the most recent Exception or Error.
     *
     * @throws \InvalidArgumentException If no Exception has been caught
     *
     * @return \Throwable|null
     */
    public function getLastException()
    {
        if (!isset($this->lastException)) {
            throw new \InvalidArgumentException('No most-recent exception');
        }

        return $this->lastException;
    }

    /**
     * Set the most recent output from evaluated code.
     */
    public function setLastStdout(string $lastStdout)
    {
        $this->lastStdout = $lastStdout;
    }

    /**
     * Get the most recent output from evaluated code.
     *
     * @throws \InvalidArgumentException If no output has happened yet
     *
     * @return string|null
     */
    public function getLastStdout()
    {
        if (!isset($this->lastStdout)) {
            throw new \InvalidArgumentException('No most-recent output');
        }

        return $this->lastStdout;
    }

    /**
     * Set the bound object ($this variable) for the interactive shell.
     *
     * Note that this unsets the bound class, if any exists.
     *
     * @param object|null $boundObject
     */
    public function setBoundObject($boundObject)
    {
        $this->boundObject = \is_object($boundObject) ? $boundObject : null;
        $this->boundClass = null;
    }

    /**
     * Get the bound object ($this variable) for the interactive shell.
     *
     * @return object|null
     */
    public function getBoundObject()
    {
        return $this->boundObject;
    }

    /**
     * Set the bound class (self) for the interactive shell.
     *
     * Note that this unsets the bound object, if any exists.
     *
     * @param string|null $boundClass
     */
    public function setBoundClass($boundClass)
    {
        $this->boundClass = (\is_string($boundClass) && $boundClass !== '') ? $boundClass : null;
        $this->boundObject = null;
    }

    /**
     * Get the bound class (self) for the interactive shell.
     *
     * @return string|null
     */
    public function getBoundClass()
    {
        return $this->boundClass;
    }

    /**
     * Set command-scope magic variables: $__class, $__file, etc.
     */
    public function setCommandScopeVariables(array $commandScopeVariables)
    {
        $vars = [];
        foreach ($commandScopeVariables as $key => $value) {
            // kind of type check
            if (\is_scalar($value) && \in_array($key, self::COMMAND_SCOPE_NAMES)) {
                $vars[$key] = $value;
            }
        }

        $this->commandScopeVariables = $vars;
    }

    /**
     * Get command-scope magic variables: $__class, $__file, etc.
     */
    public function getCommandScopeVariables(): array
    {
        return $this->commandScopeVariables;
    }

    /**
     * Get unused command-scope magic variables names: __class, __file, etc.
     *
     * This is used by the shell to unset old command-scope variables after a
     * new batch is set.
     *
     * @return array Array of unused variable names
     */
    public function getUnusedCommandScopeVariableNames(): array
    {
        return \array_diff(self::COMMAND_SCOPE_NAMES, \array_keys($this->commandScopeVariables));
    }

    /**
     * Check whether a variable name is a magic variable.
     */
    public static function isSpecialVariableName(string $name): bool
    {
        return \in_array($name, self::SPECIAL_NAMES) || \in_array($name, self::COMMAND_SCOPE_NAMES);
    }
}

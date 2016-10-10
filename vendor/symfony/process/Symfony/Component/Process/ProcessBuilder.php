<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;

/**
 * Process builder.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class ProcessBuilder
{
    private $arguments;
    private $cwd;
    private $env = array();
    private $stdin;
    private $timeout = 60;
    private $options = array();
    private $inheritEnv = true;
    private $prefix = array();

    /**
     * Constructor
     *
     * @param string[] $arguments An array of arguments
     */
    public function __construct(array $arguments = array())
    {
        $this->arguments = $arguments;
    }

    /**
     * Creates a process builder instance.
     *
     * @param string[] $arguments An array of arguments
     *
     * @return ProcessBuilder
     */
    public static function create(array $arguments = array())
    {
        return new static($arguments);
    }

    /**
     * Adds an unescaped argument to the command string.
     *
     * @param string $argument A command argument
     *
     * @return ProcessBuilder
     */
    public function add($argument)
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Adds an unescaped prefix to the command string.
     *
     * The prefix is preserved when resetting arguments.
     *
     * @param string|array $prefix A command prefix or an array of command prefixes
     *
     * @return ProcessBuilder
     */
    public function setPrefix($prefix)
    {
        $this->prefix = is_array($prefix) ? $prefix : array($prefix);

        return $this;
    }

    /**
     * Sets the arguments of the process.
     *
     * Arguments must not be escaped.
     * Previous arguments are removed.
     *
     * @param string[] $arguments
     *
     * @return ProcessBuilder
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Sets the working directory.
     *
     * @param null|string $cwd The working directory
     *
     * @return ProcessBuilder
     */
    public function setWorkingDirectory($cwd)
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Sets whether environment variables will be inherited or not.
     *
     * @param bool $inheritEnv
     *
     * @return ProcessBuilder
     */
    public function inheritEnvironmentVariables($inheritEnv = true)
    {
        $this->inheritEnv = $inheritEnv;

        return $this;
    }

    /**
     * Sets an environment variable
     *
     * Setting a variable overrides its previous value. Use `null` to unset a
     * defined environment variable.
     *
     * @param string      $name  The variable name
     * @param null|string $value The variable value
     *
     * @return ProcessBuilder
     */
    public function setEnv($name, $value)
    {
        $this->env[$name] = $value;

        return $this;
    }

    /**
     * Adds a set of environment variables.
     *
     * Already existing environment variables with the same name will be
     * overridden by the new values passed to this method. Pass `null` to unset
     * a variable.
     *
     * @param array $variables The variables
     *
     * @return ProcessBuilder
     */
    public function addEnvironmentVariables(array $variables)
    {
        $this->env = array_replace($this->env, $variables);

        return $this;
    }

    /**
     * Sets the input of the process.
     *
     * @param string|null $stdin The input as a string
     *
     * @return ProcessBuilder
     *
     * @throws InvalidArgumentException In case the argument is invalid
     */
    public function setInput($stdin)
    {
        $this->stdin = ProcessUtils::validateInput(sprintf('%s::%s', __CLASS__, __FUNCTION__), $stdin);

        return $this;
    }

    /**
     * Sets the process timeout.
     *
     * To disable the timeout, set this value to null.
     *
     * @param float|null
     *
     * @return ProcessBuilder
     *
     * @throws InvalidArgumentException
     */
    public function setTimeout($timeout)
    {
        if (null === $timeout) {
            $this->timeout = null;

            return $this;
        }

        $timeout = (float) $timeout;

        if ($timeout < 0) {
            throw new InvalidArgumentException('The timeout value must be a valid positive integer or float number.');
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Adds a proc_open option.
     *
     * @param string $name  The option name
     * @param string $value The option value
     *
     * @return ProcessBuilder
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Creates a Process instance and returns it.
     *
     * @return Process
     *
     * @throws LogicException In case no arguments have been provided
     */
    public function getProcess()
    {
        if (0 === count($this->prefix) && 0 === count($this->arguments)) {
            throw new LogicException('You must add() command arguments before calling getProcess().');
        }

        $options = $this->options;

        $arguments = array_merge($this->prefix, $this->arguments);
        $script = implode(' ', array_map(array(__NAMESPACE__.'\\ProcessUtils', 'escapeArgument'), $arguments));

        if ($this->inheritEnv) {
            // include $_ENV for BC purposes
            $env = array_replace($_ENV, $_SERVER, $this->env);
        } else {
            $env = $this->env;
        }

        return new Process($script, $this->cwd, $env, $this->stdin, $this->timeout, $options);
    }
}

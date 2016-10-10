<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Input;

/**
 * Input is the base class for all concrete Input classes.
 *
 * Three concrete classes are provided by default:
 *
 *  * `ArgvInput`: The input comes from the CLI arguments (argv)
 *  * `StringInput`: The input is provided as a string
 *  * `ArrayInput`: The input is provided as an array
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Input implements InputInterface
{
    /**
     * @var InputDefinition
     */
    protected $definition;
    protected $options = array();
    protected $arguments = array();
    protected $interactive = true;

    /**
     * Constructor.
     *
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function __construct(InputDefinition $definition = null)
    {
        if (null === $definition) {
            $this->definition = new InputDefinition();
        } else {
            $this->bind($definition);
            $this->validate();
        }
    }

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function bind(InputDefinition $definition)
    {
        $this->arguments = array();
        $this->options = array();
        $this->definition = $definition;

        $this->parse();
    }

    /**
     * Processes command line arguments.
     */
    abstract protected function parse();

    /**
     * Validates the input.
     *
     * @throws \RuntimeException When not enough arguments are given
     */
    public function validate()
    {
        if (count($this->arguments) < $this->definition->getArgumentRequiredCount()) {
            throw new \RuntimeException('Not enough arguments.');
        }
    }

    /**
     * Checks if the input is interactive.
     *
     * @return bool    Returns true if the input is interactive
     */
    public function isInteractive()
    {
        return $this->interactive;
    }

    /**
     * Sets the input interactivity.
     *
     * @param bool    $interactive If the input should be interactive
     */
    public function setInteractive($interactive)
    {
        $this->interactive = (bool) $interactive;
    }

    /**
     * Returns the argument values.
     *
     * @return array An array of argument values
     */
    public function getArguments()
    {
        return array_merge($this->definition->getArgumentDefaults(), $this->arguments);
    }

    /**
     * Returns the argument value for a given argument name.
     *
     * @param string $name The argument name
     *
     * @return mixed The argument value
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     */
    public function getArgument($name)
    {
        if (!$this->definition->hasArgument($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
        }

        return isset($this->arguments[$name]) ? $this->arguments[$name] : $this->definition->getArgument($name)->getDefault();
    }

    /**
     * Sets an argument value by name.
     *
     * @param string $name  The argument name
     * @param string $value The argument value
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     */
    public function setArgument($name, $value)
    {
        if (!$this->definition->hasArgument($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
        }

        $this->arguments[$name] = $value;
    }

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
     * @param string|int     $name The InputArgument name or position
     *
     * @return bool    true if the InputArgument object exists, false otherwise
     */
    public function hasArgument($name)
    {
        return $this->definition->hasArgument($name);
    }

    /**
     * Returns the options values.
     *
     * @return array An array of option values
     */
    public function getOptions()
    {
        return array_merge($this->definition->getOptionDefaults(), $this->options);
    }

    /**
     * Returns the option value for a given option name.
     *
     * @param string $name The option name
     *
     * @return mixed The option value
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    public function getOption($name)
    {
        if (!$this->definition->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
        }

        return isset($this->options[$name]) ? $this->options[$name] : $this->definition->getOption($name)->getDefault();
    }

    /**
     * Sets an option value by name.
     *
     * @param string         $name  The option name
     * @param string|bool    $value The option value
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    public function setOption($name, $value)
    {
        if (!$this->definition->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
        }

        $this->options[$name] = $value;
    }

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @param string $name The InputOption name
     *
     * @return bool    true if the InputOption object exists, false otherwise
     */
    public function hasOption($name)
    {
        return $this->definition->hasOption($name);
    }

    /**
     * Escapes a token through escapeshellarg if it contains unsafe chars
     *
     * @param string $token
     *
     * @return string
     */
    public function escapeToken($token)
    {
        return preg_match('{^[\w-]+$}', $token) ? $token : escapeshellarg($token);
    }
}

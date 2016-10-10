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
 * InputInterface is the interface implemented by all input classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface InputInterface
{
    /**
     * Returns the first argument from the raw parameters (not parsed).
     *
     * @return string The value of the first argument or null otherwise
     */
    public function getFirstArgument();

    /**
     * Returns true if the raw parameters (not parsed) contain a value.
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values The values to look for in the raw parameters (can be an array)
     *
     * @return bool    true if the value is contained in the raw parameters
     */
    public function hasParameterOption($values);

    /**
     * Returns the value of a raw option (not parsed).
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values  The value(s) to look for in the raw parameters (can be an array)
     * @param mixed        $default The default value to return if no result is found
     *
     * @return mixed The option value
     */
    public function getParameterOption($values, $default = false);

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function bind(InputDefinition $definition);

    /**
     * Validates if arguments given are correct.
     *
     * Throws an exception when not enough arguments are given.
     *
     * @throws \RuntimeException
     */
    public function validate();

    /**
     * Returns all the given arguments merged with the default values.
     *
     * @return array
     */
    public function getArguments();

    /**
     * Gets argument by name.
     *
     * @param string $name The name of the argument
     *
     * @return mixed
     */
    public function getArgument($name);

    /**
     * Sets an argument value by name.
     *
     * @param string $name  The argument name
     * @param string $value The argument value
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     */
    public function setArgument($name, $value);

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
     * @param string|int     $name The InputArgument name or position
     *
     * @return bool    true if the InputArgument object exists, false otherwise
     */
    public function hasArgument($name);

    /**
     * Returns all the given options merged with the default values.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Gets an option by name.
     *
     * @param string $name The name of the option
     *
     * @return mixed
     */
    public function getOption($name);

    /**
     * Sets an option value by name.
     *
     * @param string         $name  The option name
     * @param string|bool    $value The option value
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    public function setOption($name, $value);

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @param string $name The InputOption name
     *
     * @return bool    true if the InputOption object exists, false otherwise
     */
    public function hasOption($name);

    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive();

    /**
     * Sets the input interactivity.
     *
     * @param bool    $interactive If the input should be interactive
     */
    public function setInteractive($interactive);
}

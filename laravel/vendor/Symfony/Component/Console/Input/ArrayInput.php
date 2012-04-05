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
 * ArrayInput represents an input provided as an array.
 *
 * Usage:
 *
 *     $input = new ArrayInput(array('name' => 'foo', '--bar' => 'foobar'));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class ArrayInput extends Input
{
    private $parameters;

    /**
     * Constructor.
     *
     * @param array           $parameters An array of parameters
     * @param InputDefinition $definition A InputDefinition instance
     *
     * @api
     */
    public function __construct(array $parameters, InputDefinition $definition = null)
    {
        $this->parameters = $parameters;

        parent::__construct($definition);
    }

    /**
     * Returns the first argument from the raw parameters (not parsed).
     *
     * @return string The value of the first argument or null otherwise
     */
    public function getFirstArgument()
    {
        foreach ($this->parameters as $key => $value) {
            if ($key && '-' === $key[0]) {
                continue;
            }

            return $value;
        }
    }

    /**
     * Returns true if the raw parameters (not parsed) contain a value.
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values The values to look for in the raw parameters (can be an array)
     *
     * @return Boolean true if the value is contained in the raw parameters
     */
    public function hasParameterOption($values)
    {
        $values = (array) $values;

        foreach ($this->parameters as $k => $v) {
            if (!is_int($k)) {
                $v = $k;
            }

            if (in_array($v, $values)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the value of a raw option (not parsed).
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values The value(s) to look for in the raw parameters (can be an array)
     * @param mixed $default The default value to return if no result is found
     *
     * @return mixed The option value
     */
    public function getParameterOption($values, $default = false)
    {
        $values = (array) $values;

        foreach ($this->parameters as $k => $v) {
            if (is_int($k) && in_array($v, $values)) {
                return true;
            } elseif (in_array($k, $values)) {
                return $v;
            }
        }

        return $default;
    }

    /**
     * Processes command line arguments.
     */
    protected function parse()
    {
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, '--')) {
                $this->addLongOption(substr($key, 2), $value);
            } elseif ('-' === $key[0]) {
                $this->addShortOption(substr($key, 1), $value);
            } else {
                $this->addArgument($key, $value);
            }
        }
    }

    /**
     * Adds a short option value.
     *
     * @param string $shortcut The short option key
     * @param mixed  $value    The value for the option
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    private function addShortOption($shortcut, $value)
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            throw new \InvalidArgumentException(sprintf('The "-%s" option does not exist.', $shortcut));
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }

    /**
     * Adds a long option value.
     *
     * @param string $name  The long option key
     * @param mixed  $value The value for the option
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     * @throws \InvalidArgumentException When a required value is missing
     */
    private function addLongOption($name, $value)
    {
        if (!$this->definition->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
        }

        $option = $this->definition->getOption($name);

        if (null === $value) {
            if ($option->isValueRequired()) {
                throw new \InvalidArgumentException(sprintf('The "--%s" option requires a value.', $name));
            }

            $value = $option->isValueOptional() ? $option->getDefault() : true;
        }

        $this->options[$name] = $value;
    }

    /**
     * Adds an argument value.
     *
     * @param string $name  The argument name
     * @param mixed  $value The value for the argument
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     */
    private function addArgument($name, $value)
    {
        if (!$this->definition->hasArgument($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
        }

        $this->arguments[$name] = $value;
    }
}

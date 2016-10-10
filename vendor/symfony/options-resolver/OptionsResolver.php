<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\OptionsResolver;

use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Validates options and merges them with default values.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class OptionsResolver implements Options
{
    /**
     * The names of all defined options.
     *
     * @var array
     */
    private $defined = array();

    /**
     * The default option values.
     *
     * @var array
     */
    private $defaults = array();

    /**
     * The names of required options.
     *
     * @var array
     */
    private $required = array();

    /**
     * The resolved option values.
     *
     * @var array
     */
    private $resolved = array();

    /**
     * A list of normalizer closures.
     *
     * @var \Closure[]
     */
    private $normalizers = array();

    /**
     * A list of accepted values for each option.
     *
     * @var array
     */
    private $allowedValues = array();

    /**
     * A list of accepted types for each option.
     *
     * @var array
     */
    private $allowedTypes = array();

    /**
     * A list of closures for evaluating lazy options.
     *
     * @var array
     */
    private $lazy = array();

    /**
     * A list of lazy options whose closure is currently being called.
     *
     * This list helps detecting circular dependencies between lazy options.
     *
     * @var array
     */
    private $calling = array();

    /**
     * Whether the instance is locked for reading.
     *
     * Once locked, the options cannot be changed anymore. This is
     * necessary in order to avoid inconsistencies during the resolving
     * process. If any option is changed after being read, all evaluated
     * lazy options that depend on this option would become invalid.
     *
     * @var bool
     */
    private $locked = false;

    private static $typeAliases = array(
        'boolean' => 'bool',
        'integer' => 'int',
        'double' => 'float',
    );

    /**
     * Sets the default value of a given option.
     *
     * If the default value should be set based on other options, you can pass
     * a closure with the following signature:
     *
     *     function (Options $options) {
     *         // ...
     *     }
     *
     * The closure will be evaluated when {@link resolve()} is called. The
     * closure has access to the resolved values of other options through the
     * passed {@link Options} instance:
     *
     *     function (Options $options) {
     *         if (isset($options['port'])) {
     *             // ...
     *         }
     *     }
     *
     * If you want to access the previously set default value, add a second
     * argument to the closure's signature:
     *
     *     $options->setDefault('name', 'Default Name');
     *
     *     $options->setDefault('name', function (Options $options, $previousValue) {
     *         // 'Default Name' === $previousValue
     *     });
     *
     * This is mostly useful if the configuration of the {@link Options} object
     * is spread across different locations of your code, such as base and
     * sub-classes.
     *
     * @param string $option The name of the option
     * @param mixed  $value  The default value of the option
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setDefault($option, $value)
    {
        // Setting is not possible once resolving starts, because then lazy
        // options could manipulate the state of the object, leading to
        // inconsistent results.
        if ($this->locked) {
            throw new AccessException('Default values cannot be set from a lazy option or normalizer.');
        }

        // If an option is a closure that should be evaluated lazily, store it
        // in the "lazy" property.
        if ($value instanceof \Closure) {
            $reflClosure = new \ReflectionFunction($value);
            $params = $reflClosure->getParameters();

            if (isset($params[0]) && null !== ($class = $params[0]->getClass()) && Options::class === $class->name) {
                // Initialize the option if no previous value exists
                if (!isset($this->defaults[$option])) {
                    $this->defaults[$option] = null;
                }

                // Ignore previous lazy options if the closure has no second parameter
                if (!isset($this->lazy[$option]) || !isset($params[1])) {
                    $this->lazy[$option] = array();
                }

                // Store closure for later evaluation
                $this->lazy[$option][] = $value;
                $this->defined[$option] = true;

                // Make sure the option is processed
                unset($this->resolved[$option]);

                return $this;
            }
        }

        // This option is not lazy anymore
        unset($this->lazy[$option]);

        // Yet undefined options can be marked as resolved, because we only need
        // to resolve options with lazy closures, normalizers or validation
        // rules, none of which can exist for undefined options
        // If the option was resolved before, update the resolved value
        if (!isset($this->defined[$option]) || array_key_exists($option, $this->resolved)) {
            $this->resolved[$option] = $value;
        }

        $this->defaults[$option] = $value;
        $this->defined[$option] = true;

        return $this;
    }

    /**
     * Sets a list of default values.
     *
     * @param array $defaults The default values to set
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setDefaults(array $defaults)
    {
        foreach ($defaults as $option => $value) {
            $this->setDefault($option, $value);
        }

        return $this;
    }

    /**
     * Returns whether a default value is set for an option.
     *
     * Returns true if {@link setDefault()} was called for this option.
     * An option is also considered set if it was set to null.
     *
     * @param string $option The option name
     *
     * @return bool Whether a default value is set
     */
    public function hasDefault($option)
    {
        return array_key_exists($option, $this->defaults);
    }

    /**
     * Marks one or more options as required.
     *
     * @param string|string[] $optionNames One or more option names
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setRequired($optionNames)
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be made required from a lazy option or normalizer.');
        }

        foreach ((array) $optionNames as $option) {
            $this->defined[$option] = true;
            $this->required[$option] = true;
        }

        return $this;
    }

    /**
     * Returns whether an option is required.
     *
     * An option is required if it was passed to {@link setRequired()}.
     *
     * @param string $option The name of the option
     *
     * @return bool Whether the option is required
     */
    public function isRequired($option)
    {
        return isset($this->required[$option]);
    }

    /**
     * Returns the names of all required options.
     *
     * @return string[] The names of the required options
     *
     * @see isRequired()
     */
    public function getRequiredOptions()
    {
        return array_keys($this->required);
    }

    /**
     * Returns whether an option is missing a default value.
     *
     * An option is missing if it was passed to {@link setRequired()}, but not
     * to {@link setDefault()}. This option must be passed explicitly to
     * {@link resolve()}, otherwise an exception will be thrown.
     *
     * @param string $option The name of the option
     *
     * @return bool Whether the option is missing
     */
    public function isMissing($option)
    {
        return isset($this->required[$option]) && !array_key_exists($option, $this->defaults);
    }

    /**
     * Returns the names of all options missing a default value.
     *
     * @return string[] The names of the missing options
     *
     * @see isMissing()
     */
    public function getMissingOptions()
    {
        return array_keys(array_diff_key($this->required, $this->defaults));
    }

    /**
     * Defines a valid option name.
     *
     * Defines an option name without setting a default value. The option will
     * be accepted when passed to {@link resolve()}. When not passed, the
     * option will not be included in the resolved options.
     *
     * @param string|string[] $optionNames One or more option names
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setDefined($optionNames)
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be defined from a lazy option or normalizer.');
        }

        foreach ((array) $optionNames as $option) {
            $this->defined[$option] = true;
        }

        return $this;
    }

    /**
     * Returns whether an option is defined.
     *
     * Returns true for any option passed to {@link setDefault()},
     * {@link setRequired()} or {@link setDefined()}.
     *
     * @param string $option The option name
     *
     * @return bool Whether the option is defined
     */
    public function isDefined($option)
    {
        return isset($this->defined[$option]);
    }

    /**
     * Returns the names of all defined options.
     *
     * @return string[] The names of the defined options
     *
     * @see isDefined()
     */
    public function getDefinedOptions()
    {
        return array_keys($this->defined);
    }

    /**
     * Sets the normalizer for an option.
     *
     * The normalizer should be a closure with the following signature:
     *
     * ```php
     * function (Options $options, $value) {
     *     // ...
     * }
     * ```
     *
     * The closure is invoked when {@link resolve()} is called. The closure
     * has access to the resolved values of other options through the passed
     * {@link Options} instance.
     *
     * The second parameter passed to the closure is the value of
     * the option.
     *
     * The resolved option value is set to the return value of the closure.
     *
     * @param string   $option     The option name
     * @param \Closure $normalizer The normalizer
     *
     * @return OptionsResolver This instance
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function setNormalizer($option, \Closure $normalizer)
    {
        if ($this->locked) {
            throw new AccessException('Normalizers cannot be set from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(sprintf(
                'The option "%s" does not exist. Defined options are: "%s".',
                $option,
                implode('", "', array_keys($this->defined))
            ));
        }

        $this->normalizers[$option] = $normalizer;

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Sets allowed values for an option.
     *
     * Instead of passing values, you may also pass a closures with the
     * following signature:
     *
     *     function ($value) {
     *         // return true or false
     *     }
     *
     * The closure receives the value as argument and should return true to
     * accept the value and false to reject the value.
     *
     * @param string $option        The option name
     * @param mixed  $allowedValues One or more acceptable values/closures
     *
     * @return OptionsResolver This instance
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function setAllowedValues($option, $allowedValues)
    {
        if ($this->locked) {
            throw new AccessException('Allowed values cannot be set from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(sprintf(
                'The option "%s" does not exist. Defined options are: "%s".',
                $option,
                implode('", "', array_keys($this->defined))
            ));
        }

        $this->allowedValues[$option] = is_array($allowedValues) ? $allowedValues : array($allowedValues);

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Adds allowed values for an option.
     *
     * The values are merged with the allowed values defined previously.
     *
     * Instead of passing values, you may also pass a closures with the
     * following signature:
     *
     *     function ($value) {
     *         // return true or false
     *     }
     *
     * The closure receives the value as argument and should return true to
     * accept the value and false to reject the value.
     *
     * @param string $option        The option name
     * @param mixed  $allowedValues One or more acceptable values/closures
     *
     * @return OptionsResolver This instance
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function addAllowedValues($option, $allowedValues)
    {
        if ($this->locked) {
            throw new AccessException('Allowed values cannot be added from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(sprintf(
                'The option "%s" does not exist. Defined options are: "%s".',
                $option,
                implode('", "', array_keys($this->defined))
            ));
        }

        if (!is_array($allowedValues)) {
            $allowedValues = array($allowedValues);
        }

        if (!isset($this->allowedValues[$option])) {
            $this->allowedValues[$option] = $allowedValues;
        } else {
            $this->allowedValues[$option] = array_merge($this->allowedValues[$option], $allowedValues);
        }

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Sets allowed types for an option.
     *
     * Any type for which a corresponding is_<type>() function exists is
     * acceptable. Additionally, fully-qualified class or interface names may
     * be passed.
     *
     * @param string          $option       The option name
     * @param string|string[] $allowedTypes One or more accepted types
     *
     * @return OptionsResolver This instance
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function setAllowedTypes($option, $allowedTypes)
    {
        if ($this->locked) {
            throw new AccessException('Allowed types cannot be set from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(sprintf(
                'The option "%s" does not exist. Defined options are: "%s".',
                $option,
                implode('", "', array_keys($this->defined))
            ));
        }

        $this->allowedTypes[$option] = (array) $allowedTypes;

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Adds allowed types for an option.
     *
     * The types are merged with the allowed types defined previously.
     *
     * Any type for which a corresponding is_<type>() function exists is
     * acceptable. Additionally, fully-qualified class or interface names may
     * be passed.
     *
     * @param string          $option       The option name
     * @param string|string[] $allowedTypes One or more accepted types
     *
     * @return OptionsResolver This instance
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function addAllowedTypes($option, $allowedTypes)
    {
        if ($this->locked) {
            throw new AccessException('Allowed types cannot be added from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(sprintf(
                'The option "%s" does not exist. Defined options are: "%s".',
                $option,
                implode('", "', array_keys($this->defined))
            ));
        }

        if (!isset($this->allowedTypes[$option])) {
            $this->allowedTypes[$option] = (array) $allowedTypes;
        } else {
            $this->allowedTypes[$option] = array_merge($this->allowedTypes[$option], (array) $allowedTypes);
        }

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Removes the option with the given name.
     *
     * Undefined options are ignored.
     *
     * @param string|string[] $optionNames One or more option names
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function remove($optionNames)
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be removed from a lazy option or normalizer.');
        }

        foreach ((array) $optionNames as $option) {
            unset($this->defined[$option], $this->defaults[$option], $this->required[$option], $this->resolved[$option]);
            unset($this->lazy[$option], $this->normalizers[$option], $this->allowedTypes[$option], $this->allowedValues[$option]);
        }

        return $this;
    }

    /**
     * Removes all options.
     *
     * @return OptionsResolver This instance
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function clear()
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be cleared from a lazy option or normalizer.');
        }

        $this->defined = array();
        $this->defaults = array();
        $this->required = array();
        $this->resolved = array();
        $this->lazy = array();
        $this->normalizers = array();
        $this->allowedTypes = array();
        $this->allowedValues = array();

        return $this;
    }

    /**
     * Merges options with the default values stored in the container and
     * validates them.
     *
     * Exceptions are thrown if:
     *
     *  - Undefined options are passed;
     *  - Required options are missing;
     *  - Options have invalid types;
     *  - Options have invalid values.
     *
     * @param array $options A map of option names to values
     *
     * @return array The merged and validated options
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     * @throws MissingOptionsException   If a required option is missing
     * @throws OptionDefinitionException If there is a cyclic dependency between
     *                                   lazy options and/or normalizers
     * @throws NoSuchOptionException     If a lazy option reads an unavailable option
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function resolve(array $options = array())
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be resolved from a lazy option or normalizer.');
        }

        // Allow this method to be called multiple times
        $clone = clone $this;

        // Make sure that no unknown options are passed
        $diff = array_diff_key($options, $clone->defined);

        if (count($diff) > 0) {
            ksort($clone->defined);
            ksort($diff);

            throw new UndefinedOptionsException(sprintf(
                (count($diff) > 1 ? 'The options "%s" do not exist.' : 'The option "%s" does not exist.').' Defined options are: "%s".',
                implode('", "', array_keys($diff)),
                implode('", "', array_keys($clone->defined))
            ));
        }

        // Override options set by the user
        foreach ($options as $option => $value) {
            $clone->defaults[$option] = $value;
            unset($clone->resolved[$option], $clone->lazy[$option]);
        }

        // Check whether any required option is missing
        $diff = array_diff_key($clone->required, $clone->defaults);

        if (count($diff) > 0) {
            ksort($diff);

            throw new MissingOptionsException(sprintf(
                count($diff) > 1 ? 'The required options "%s" are missing.' : 'The required option "%s" is missing.',
                implode('", "', array_keys($diff))
            ));
        }

        // Lock the container
        $clone->locked = true;

        // Now process the individual options. Use offsetGet(), which resolves
        // the option itself and any options that the option depends on
        foreach ($clone->defaults as $option => $_) {
            $clone->offsetGet($option);
        }

        return $clone->resolved;
    }

    /**
     * Returns the resolved value of an option.
     *
     * @param string $option The option name
     *
     * @return mixed The option value
     *
     * @throws AccessException           If accessing this method outside of
     *                                   {@link resolve()}
     * @throws NoSuchOptionException     If the option is not set
     * @throws InvalidOptionsException   If the option doesn't fulfill the
     *                                   specified validation rules
     * @throws OptionDefinitionException If there is a cyclic dependency between
     *                                   lazy options and/or normalizers
     */
    public function offsetGet($option)
    {
        if (!$this->locked) {
            throw new AccessException('Array access is only supported within closures of lazy options and normalizers.');
        }

        // Shortcut for resolved options
        if (array_key_exists($option, $this->resolved)) {
            return $this->resolved[$option];
        }

        // Check whether the option is set at all
        if (!array_key_exists($option, $this->defaults)) {
            if (!isset($this->defined[$option])) {
                throw new NoSuchOptionException(sprintf(
                    'The option "%s" does not exist. Defined options are: "%s".',
                    $option,
                    implode('", "', array_keys($this->defined))
                ));
            }

            throw new NoSuchOptionException(sprintf(
                'The optional option "%s" has no value set. You should make sure it is set with "isset" before reading it.',
                $option
            ));
        }

        $value = $this->defaults[$option];

        // Resolve the option if the default value is lazily evaluated
        if (isset($this->lazy[$option])) {
            // If the closure is already being called, we have a cyclic
            // dependency
            if (isset($this->calling[$option])) {
                throw new OptionDefinitionException(sprintf(
                    'The options "%s" have a cyclic dependency.',
                    implode('", "', array_keys($this->calling))
                ));
            }

            // The following section must be protected from cyclic
            // calls. Set $calling for the current $option to detect a cyclic
            // dependency
            // BEGIN
            $this->calling[$option] = true;
            try {
                foreach ($this->lazy[$option] as $closure) {
                    $value = $closure($this, $value);
                }
            } finally {
                unset($this->calling[$option]);
            }
            // END
        }

        // Validate the type of the resolved option
        if (isset($this->allowedTypes[$option])) {
            $valid = false;

            foreach ($this->allowedTypes[$option] as $type) {
                $type = isset(self::$typeAliases[$type]) ? self::$typeAliases[$type] : $type;

                if (function_exists($isFunction = 'is_'.$type)) {
                    if ($isFunction($value)) {
                        $valid = true;
                        break;
                    }

                    continue;
                }

                if ($value instanceof $type) {
                    $valid = true;
                    break;
                }
            }

            if (!$valid) {
                throw new InvalidOptionsException(sprintf(
                    'The option "%s" with value %s is expected to be of type '.
                    '"%s", but is of type "%s".',
                    $option,
                    $this->formatValue($value),
                    implode('" or "', $this->allowedTypes[$option]),
                    $this->formatTypeOf($value)
                ));
            }
        }

        // Validate the value of the resolved option
        if (isset($this->allowedValues[$option])) {
            $success = false;
            $printableAllowedValues = array();

            foreach ($this->allowedValues[$option] as $allowedValue) {
                if ($allowedValue instanceof \Closure) {
                    if ($allowedValue($value)) {
                        $success = true;
                        break;
                    }

                    // Don't include closures in the exception message
                    continue;
                } elseif ($value === $allowedValue) {
                    $success = true;
                    break;
                }

                $printableAllowedValues[] = $allowedValue;
            }

            if (!$success) {
                $message = sprintf(
                    'The option "%s" with value %s is invalid.',
                    $option,
                    $this->formatValue($value)
                );

                if (count($printableAllowedValues) > 0) {
                    $message .= sprintf(
                        ' Accepted values are: %s.',
                        $this->formatValues($printableAllowedValues)
                    );
                }

                throw new InvalidOptionsException($message);
            }
        }

        // Normalize the validated option
        if (isset($this->normalizers[$option])) {
            // If the closure is already being called, we have a cyclic
            // dependency
            if (isset($this->calling[$option])) {
                throw new OptionDefinitionException(sprintf(
                    'The options "%s" have a cyclic dependency.',
                    implode('", "', array_keys($this->calling))
                ));
            }

            $normalizer = $this->normalizers[$option];

            // The following section must be protected from cyclic
            // calls. Set $calling for the current $option to detect a cyclic
            // dependency
            // BEGIN
            $this->calling[$option] = true;
            try {
                $value = $normalizer($this, $value);
            } finally {
                unset($this->calling[$option]);
            }
            // END
        }

        // Mark as resolved
        $this->resolved[$option] = $value;

        return $value;
    }

    /**
     * Returns whether a resolved option with the given name exists.
     *
     * @param string $option The option name
     *
     * @return bool Whether the option is set
     *
     * @throws AccessException If accessing this method outside of {@link resolve()}
     *
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($option)
    {
        if (!$this->locked) {
            throw new AccessException('Array access is only supported within closures of lazy options and normalizers.');
        }

        return array_key_exists($option, $this->defaults);
    }

    /**
     * Not supported.
     *
     * @throws AccessException
     */
    public function offsetSet($option, $value)
    {
        throw new AccessException('Setting options via array access is not supported. Use setDefault() instead.');
    }

    /**
     * Not supported.
     *
     * @throws AccessException
     */
    public function offsetUnset($option)
    {
        throw new AccessException('Removing options via array access is not supported. Use remove() instead.');
    }

    /**
     * Returns the number of set options.
     *
     * This may be only a subset of the defined options.
     *
     * @return int Number of options
     *
     * @throws AccessException If accessing this method outside of {@link resolve()}
     *
     * @see \Countable::count()
     */
    public function count()
    {
        if (!$this->locked) {
            throw new AccessException('Counting is only supported within closures of lazy options and normalizers.');
        }

        return count($this->defaults);
    }

    /**
     * Returns a string representation of the type of the value.
     *
     * This method should be used if you pass the type of a value as
     * message parameter to a constraint violation. Note that such
     * parameters should usually not be included in messages aimed at
     * non-technical people.
     *
     * @param mixed $value The value to return the type of
     *
     * @return string The type of the value
     */
    private function formatTypeOf($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * Returns a string representation of the value.
     *
     * This method returns the equivalent PHP tokens for most scalar types
     * (i.e. "false" for false, "1" for 1 etc.). Strings are always wrapped
     * in double quotes (").
     *
     * @param mixed $value The value to format as string
     *
     * @return string The string representation of the passed value
     */
    private function formatValue($value)
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_string($value)) {
            return '"'.$value.'"';
        }

        if (is_resource($value)) {
            return 'resource';
        }

        if (null === $value) {
            return 'null';
        }

        if (false === $value) {
            return 'false';
        }

        if (true === $value) {
            return 'true';
        }

        return (string) $value;
    }

    /**
     * Returns a string representation of a list of values.
     *
     * Each of the values is converted to a string using
     * {@link formatValue()}. The values are then concatenated with commas.
     *
     * @param array $values A list of values
     *
     * @return string The string representation of the value list
     *
     * @see formatValue()
     */
    private function formatValues(array $values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->formatValue($value);
        }

        return implode(', ', $values);
    }
}

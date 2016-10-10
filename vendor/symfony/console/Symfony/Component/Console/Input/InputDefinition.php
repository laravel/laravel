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

if (!defined('JSON_UNESCAPED_UNICODE')) {
    define('JSON_UNESCAPED_SLASHES', 64);
    define('JSON_UNESCAPED_UNICODE', 256);
}

use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * A InputDefinition represents a set of valid command line arguments and options.
 *
 * Usage:
 *
 *     $definition = new InputDefinition(array(
 *       new InputArgument('name', InputArgument::REQUIRED),
 *       new InputOption('foo', 'f', InputOption::VALUE_REQUIRED),
 *     ));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class InputDefinition
{
    private $arguments;
    private $requiredCount;
    private $hasAnArrayArgument = false;
    private $hasOptional;
    private $options;
    private $shortcuts;

    /**
     * Constructor.
     *
     * @param array $definition An array of InputArgument and InputOption instance
     *
     * @api
     */
    public function __construct(array $definition = array())
    {
        $this->setDefinition($definition);
    }

    /**
     * Sets the definition of the input.
     *
     * @param array $definition The definition array
     *
     * @api
     */
    public function setDefinition(array $definition)
    {
        $arguments = array();
        $options = array();
        foreach ($definition as $item) {
            if ($item instanceof InputOption) {
                $options[] = $item;
            } else {
                $arguments[] = $item;
            }
        }

        $this->setArguments($arguments);
        $this->setOptions($options);
    }

    /**
     * Sets the InputArgument objects.
     *
     * @param InputArgument[] $arguments An array of InputArgument objects
     *
     * @api
     */
    public function setArguments($arguments = array())
    {
        $this->arguments          = array();
        $this->requiredCount      = 0;
        $this->hasOptional        = false;
        $this->hasAnArrayArgument = false;
        $this->addArguments($arguments);
    }

    /**
     * Adds an array of InputArgument objects.
     *
     * @param InputArgument[] $arguments An array of InputArgument objects
     *
     * @api
     */
    public function addArguments($arguments = array())
    {
        if (null !== $arguments) {
            foreach ($arguments as $argument) {
                $this->addArgument($argument);
            }
        }
    }

    /**
     * Adds an InputArgument object.
     *
     * @param InputArgument $argument An InputArgument object
     *
     * @throws \LogicException When incorrect argument is given
     *
     * @api
     */
    public function addArgument(InputArgument $argument)
    {
        if (isset($this->arguments[$argument->getName()])) {
            throw new \LogicException(sprintf('An argument with name "%s" already exists.', $argument->getName()));
        }

        if ($this->hasAnArrayArgument) {
            throw new \LogicException('Cannot add an argument after an array argument.');
        }

        if ($argument->isRequired() && $this->hasOptional) {
            throw new \LogicException('Cannot add a required argument after an optional one.');
        }

        if ($argument->isArray()) {
            $this->hasAnArrayArgument = true;
        }

        if ($argument->isRequired()) {
            ++$this->requiredCount;
        } else {
            $this->hasOptional = true;
        }

        $this->arguments[$argument->getName()] = $argument;
    }

    /**
     * Returns an InputArgument by name or by position.
     *
     * @param string|int     $name The InputArgument name or position
     *
     * @return InputArgument An InputArgument object
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     *
     * @api
     */
    public function getArgument($name)
    {
        if (!$this->hasArgument($name)) {
            throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
        }

        $arguments = is_int($name) ? array_values($this->arguments) : $this->arguments;

        return $arguments[$name];
    }

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
     * @param string|int     $name The InputArgument name or position
     *
     * @return bool    true if the InputArgument object exists, false otherwise
     *
     * @api
     */
    public function hasArgument($name)
    {
        $arguments = is_int($name) ? array_values($this->arguments) : $this->arguments;

        return isset($arguments[$name]);
    }

    /**
     * Gets the array of InputArgument objects.
     *
     * @return InputArgument[] An array of InputArgument objects
     *
     * @api
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns the number of InputArguments.
     *
     * @return int     The number of InputArguments
     */
    public function getArgumentCount()
    {
        return $this->hasAnArrayArgument ? PHP_INT_MAX : count($this->arguments);
    }

    /**
     * Returns the number of required InputArguments.
     *
     * @return int     The number of required InputArguments
     */
    public function getArgumentRequiredCount()
    {
        return $this->requiredCount;
    }

    /**
     * Gets the default values.
     *
     * @return array An array of default values
     */
    public function getArgumentDefaults()
    {
        $values = array();
        foreach ($this->arguments as $argument) {
            $values[$argument->getName()] = $argument->getDefault();
        }

        return $values;
    }

    /**
     * Sets the InputOption objects.
     *
     * @param InputOption[] $options An array of InputOption objects
     *
     * @api
     */
    public function setOptions($options = array())
    {
        $this->options = array();
        $this->shortcuts = array();
        $this->addOptions($options);
    }

    /**
     * Adds an array of InputOption objects.
     *
     * @param InputOption[] $options An array of InputOption objects
     *
     * @api
     */
    public function addOptions($options = array())
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * Adds an InputOption object.
     *
     * @param InputOption $option An InputOption object
     *
     * @throws \LogicException When option given already exist
     *
     * @api
     */
    public function addOption(InputOption $option)
    {
        if (isset($this->options[$option->getName()]) && !$option->equals($this->options[$option->getName()])) {
            throw new \LogicException(sprintf('An option named "%s" already exists.', $option->getName()));
        }

        if ($option->getShortcut()) {
            foreach (explode('|', $option->getShortcut()) as $shortcut) {
                if (isset($this->shortcuts[$shortcut]) && !$option->equals($this->options[$this->shortcuts[$shortcut]])) {
                    throw new \LogicException(sprintf('An option with shortcut "%s" already exists.', $shortcut));
                }
            }
        }

        $this->options[$option->getName()] = $option;
        if ($option->getShortcut()) {
            foreach (explode('|', $option->getShortcut()) as $shortcut) {
                $this->shortcuts[$shortcut] = $option->getName();
            }
        }
    }

    /**
     * Returns an InputOption by name.
     *
     * @param string $name The InputOption name
     *
     * @return InputOption A InputOption object
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     *
     * @api
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @param string $name The InputOption name
     *
     * @return bool    true if the InputOption object exists, false otherwise
     *
     * @api
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Gets the array of InputOption objects.
     *
     * @return InputOption[] An array of InputOption objects
     *
     * @api
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns true if an InputOption object exists by shortcut.
     *
     * @param string $name The InputOption shortcut
     *
     * @return bool    true if the InputOption object exists, false otherwise
     */
    public function hasShortcut($name)
    {
        return isset($this->shortcuts[$name]);
    }

    /**
     * Gets an InputOption by shortcut.
     *
     * @param string $shortcut the Shortcut name
     *
     * @return InputOption An InputOption object
     */
    public function getOptionForShortcut($shortcut)
    {
        return $this->getOption($this->shortcutToName($shortcut));
    }

    /**
     * Gets an array of default values.
     *
     * @return array An array of all default values
     */
    public function getOptionDefaults()
    {
        $values = array();
        foreach ($this->options as $option) {
            $values[$option->getName()] = $option->getDefault();
        }

        return $values;
    }

    /**
     * Returns the InputOption name given a shortcut.
     *
     * @param string $shortcut The shortcut
     *
     * @return string The InputOption name
     *
     * @throws \InvalidArgumentException When option given does not exist
     */
    private function shortcutToName($shortcut)
    {
        if (!isset($this->shortcuts[$shortcut])) {
            throw new \InvalidArgumentException(sprintf('The "-%s" option does not exist.', $shortcut));
        }

        return $this->shortcuts[$shortcut];
    }

    /**
     * Gets the synopsis.
     *
     * @return string The synopsis
     */
    public function getSynopsis()
    {
        $elements = array();
        foreach ($this->getOptions() as $option) {
            $shortcut = $option->getShortcut() ? sprintf('-%s|', $option->getShortcut()) : '';
            $elements[] = sprintf('['.($option->isValueRequired() ? '%s--%s="..."' : ($option->isValueOptional() ? '%s--%s[="..."]' : '%s--%s')).']', $shortcut, $option->getName());
        }

        foreach ($this->getArguments() as $argument) {
            $elements[] = sprintf($argument->isRequired() ? '%s' : '[%s]', $argument->getName().($argument->isArray() ? '1' : ''));

            if ($argument->isArray()) {
                $elements[] = sprintf('... [%sN]', $argument->getName());
            }
        }

        return implode(' ', $elements);
    }

    /**
     * Returns a textual representation of the InputDefinition.
     *
     * @return string A string representing the InputDefinition
     *
     * @deprecated Deprecated since version 2.3, to be removed in 3.0.
     */
    public function asText()
    {
        $descriptor = new TextDescriptor();
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true);
        $descriptor->describe($output, $this, array('raw_output' => true));

        return $output->fetch();
    }

    /**
     * Returns an XML representation of the InputDefinition.
     *
     * @param bool    $asDom Whether to return a DOM or an XML string
     *
     * @return string|\DOMDocument An XML string representing the InputDefinition
     *
     * @deprecated Deprecated since version 2.3, to be removed in 3.0.
     */
    public function asXml($asDom = false)
    {
        $descriptor = new XmlDescriptor();

        if ($asDom) {
            return $descriptor->getInputDefinitionDocument($this);
        }

        $output = new BufferedOutput();
        $descriptor->describe($output, $this);

        return $output->fetch();
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Field;

/**
 * ChoiceFormField represents a choice form field.
 *
 * It is constructed from a HTML select tag, or a HTML checkbox, or radio inputs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class ChoiceFormField extends FormField
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $multiple;
    /**
     * @var array
     */
    private $options;
    /**
     * @var bool
     */
    private $validationDisabled = false;

    /**
     * Returns true if the field should be included in the submitted values.
     *
     * @return bool    true if the field should be included in the submitted values, false otherwise
     */
    public function hasValue()
    {
        // don't send a value for unchecked checkboxes
        if (in_array($this->type, array('checkbox', 'radio')) && null === $this->value) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current selected option is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        foreach ($this->options as $option) {
            if ($option['value'] == $this->value && $option['disabled']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the value of the field.
     *
     * @param string $value The value of the field
     *
     * @api
     */
    public function select($value)
    {
        $this->setValue($value);
    }

    /**
     * Ticks a checkbox.
     *
     * @throws \LogicException When the type provided is not correct
     *
     * @api
     */
    public function tick()
    {
        if ('checkbox' !== $this->type) {
            throw new \LogicException(sprintf('You cannot tick "%s" as it is not a checkbox (%s).', $this->name, $this->type));
        }

        $this->setValue(true);
    }

    /**
     * Ticks a checkbox.
     *
     * @throws \LogicException When the type provided is not correct
     *
     * @api
     */
    public function untick()
    {
        if ('checkbox' !== $this->type) {
            throw new \LogicException(sprintf('You cannot tick "%s" as it is not a checkbox (%s).', $this->name, $this->type));
        }

        $this->setValue(false);
    }

    /**
     * Sets the value of the field.
     *
     * @param string $value The value of the field
     *
     * @throws \InvalidArgumentException When value type provided is not correct
     */
    public function setValue($value)
    {
        if ('checkbox' === $this->type && false === $value) {
            // uncheck
            $this->value = null;
        } elseif ('checkbox' === $this->type && true === $value) {
            // check
            $this->value = $this->options[0]['value'];
        } else {
            if (is_array($value)) {
                if (!$this->multiple) {
                    throw new \InvalidArgumentException(sprintf('The value for "%s" cannot be an array.', $this->name));
                }

                foreach ($value as $v) {
                    if (!$this->containsOption($v, $this->options)) {
                        throw new \InvalidArgumentException(sprintf('Input "%s" cannot take "%s" as a value (possible values: %s).', $this->name, $v, implode(', ', $this->availableOptionValues())));
                    }
                }
            } elseif (!$this->containsOption($value, $this->options)) {
                throw new \InvalidArgumentException(sprintf('Input "%s" cannot take "%s" as a value (possible values: %s).', $this->name, $value, implode(', ', $this->availableOptionValues())));
            }

            if ($this->multiple) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                $this->value = $value;
            } else {
                parent::setValue($value);
            }
        }
    }

    /**
     * Adds a choice to the current ones.
     *
     * This method should only be used internally.
     *
     * @param \DOMNode $node A \DOMNode
     *
     * @throws \LogicException When choice provided is not multiple nor radio
     */
    public function addChoice(\DOMNode $node)
    {
        if (!$this->multiple && 'radio' !== $this->type) {
            throw new \LogicException(sprintf('Unable to add a choice for "%s" as it is not multiple or is not a radio button.', $this->name));
        }

        $option = $this->buildOptionValue($node);
        $this->options[] = $option;

        if ($node->hasAttribute('checked')) {
            $this->value = $option['value'];
        }
    }

    /**
     * Returns the type of the choice field (radio, select, or checkbox).
     *
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns true if the field accepts multiple values.
     *
     * @return bool    true if the field accepts multiple values, false otherwise
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Initializes the form field.
     *
     * @throws \LogicException When node type is incorrect
     */
    protected function initialize()
    {
        if ('input' !== $this->node->nodeName && 'select' !== $this->node->nodeName) {
            throw new \LogicException(sprintf('A ChoiceFormField can only be created from an input or select tag (%s given).', $this->node->nodeName));
        }

        if ('input' === $this->node->nodeName && 'checkbox' !== strtolower($this->node->getAttribute('type')) && 'radio' !== strtolower($this->node->getAttribute('type'))) {
            throw new \LogicException(sprintf('A ChoiceFormField can only be created from an input tag with a type of checkbox or radio (given type is %s).', $this->node->getAttribute('type')));
        }

        $this->value = null;
        $this->options = array();
        $this->multiple = false;

        if ('input' == $this->node->nodeName) {
            $this->type = strtolower($this->node->getAttribute('type'));
            $optionValue = $this->buildOptionValue($this->node);
            $this->options[] = $optionValue;

            if ($this->node->hasAttribute('checked')) {
                $this->value = $optionValue['value'];
            }
        } else {
            $this->type = 'select';
            if ($this->node->hasAttribute('multiple')) {
                $this->multiple = true;
                $this->value = array();
                $this->name = str_replace('[]', '', $this->name);
            }

            $found = false;
            foreach ($this->xpath->query('descendant::option', $this->node) as $option) {
                $optionValue = $this->buildOptionValue($option);
                $this->options[] = $optionValue;

                if ($option->hasAttribute('selected')) {
                    $found = true;
                    if ($this->multiple) {
                        $this->value[] = $optionValue['value'];
                    } else {
                        $this->value = $optionValue['value'];
                    }
                }
            }

            // if no option is selected and if it is a simple select box, take the first option as the value
            if (!$found && !$this->multiple && !empty($this->options)) {
                $this->value = $this->options[0]['value'];
            }
        }
    }

    /**
     * Returns option value with associated disabled flag
     *
     * @param \DOMNode $node
     *
     * @return array
     */
    private function buildOptionValue($node)
    {
        $option = array();

        $defaultValue = (isset($node->nodeValue) && !empty($node->nodeValue)) ? $node->nodeValue : '1';
        $option['value'] = $node->hasAttribute('value') ? $node->getAttribute('value') : $defaultValue;
        $option['disabled'] = $node->hasAttribute('disabled');

        return $option;
    }

    /**
     * Checks whether given value is in the existing options
     *
     * @param string $optionValue
     * @param array  $options
     *
     * @return bool
     */
    public function containsOption($optionValue, $options)
    {
        if ($this->validationDisabled) {
            return true;
        }

        foreach ($options as $option) {
            if ($option['value'] == $optionValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns list of available field options
     *
     * @return array
     */
    public function availableOptionValues()
    {
        $values = array();

        foreach ($this->options as $option) {
            $values[] = $option['value'];
        }

        return $values;
    }

    /**
     * Disables the internal validation of the field.
     *
     * @return self
     */
    public function disableValidation()
    {
        $this->validationDisabled = true;

        return $this;
    }
}

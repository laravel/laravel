<?php

namespace TijsVerkoyen\CssToInlineStyles\Css\Property;

use Symfony\Component\CssSelector\Node\Specificity;

final class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var Specificity|null
     */
    private $originalSpecificity;

    /**
     * Property constructor.
     * @param string           $name
     * @param string           $value
     * @param Specificity|null $specificity
     */
    public function __construct($name, $value, ?Specificity $specificity = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->originalSpecificity = $specificity;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get originalSpecificity
     *
     * @return Specificity|null
     */
    public function getOriginalSpecificity()
    {
        return $this->originalSpecificity;
    }

    /**
     * Is this property important?
     *
     * @return bool
     */
    public function isImportant()
    {
        return (stripos($this->value, '!important') !== false);
    }

    /**
     * Get the textual representation of the property
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            '%1$s: %2$s;',
            $this->name,
            $this->value
        );
    }
}

<?php

namespace DeepCopy\TypeMatcher;

class TypeMatcher
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $element
     *
     * @return boolean
     */
    public function matches($element)
    {
        return is_object($element) ? is_a($element, $this->type) : gettype($element) === $this->type;
    }
}

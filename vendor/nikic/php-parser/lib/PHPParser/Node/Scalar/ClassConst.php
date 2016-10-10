<?php

class PHPParser_Node_Scalar_ClassConst extends PHPParser_Node_Scalar
{
    /**
     * Constructs a __CLASS__ const node
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct(array(), $attributes);
    }
}
<?php

/**
 * @property string|PHPParser_Node_Expr $name Name
 */
class PHPParser_Node_Expr_Variable extends PHPParser_Node_Expr
{
    /**
     * Constructs a variable node.
     *
     * @param string|PHPParser_Node_Expr $name       Name
     * @param array                      $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct(
            array(
                 'name' => $name
            ),
            $attributes
        );
    }
}
<?php

/**
 * @property PHPParser_Node_Expr        $var  Variable holding object
 * @property string|PHPParser_Node_Expr $name Property Name
 */
class PHPParser_Node_Expr_PropertyFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Expr        $var        Variable holding object
     * @param string|PHPParser_Node_Expr $name       Property name
     * @param array                      $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $var, $name, array $attributes = array()) {
        parent::__construct(
            array(
                'var'  => $var,
                'name' => $name
            ),
            $attributes
        );
    }
}
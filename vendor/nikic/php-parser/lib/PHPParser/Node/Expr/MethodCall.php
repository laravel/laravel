<?php

/**
 * @property PHPParser_Node_Expr        $var  Variable holding object
 * @property string|PHPParser_Node_Expr $name Method name
 * @property PHPParser_Node_Arg[]       $args Arguments
 */
class PHPParser_Node_Expr_MethodCall extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Expr        $var        Variable holding object
     * @param string|PHPParser_Node_Expr $name       Method name
     * @param PHPParser_Node_Arg[]       $args       Arguments
     * @param array                      $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $var, $name, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'var'  => $var,
                'name' => $name,
                'args' => $args
            ),
            $attributes
        );
    }
}
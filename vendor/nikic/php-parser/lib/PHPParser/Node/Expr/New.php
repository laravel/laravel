<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 * @property PHPParser_Node_Arg[]                    $args  Arguments
 */
class PHPParser_Node_Expr_New extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param PHPParser_Node_Arg[]                    $args       Arguments
     * @param array                                   $attributes Additional attributes
     */
    public function __construct($class, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'class' => $class,
                'args'  => $args
            ),
            $attributes
        );
    }
}
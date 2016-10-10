<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $name Function name
 * @property PHPParser_Node_Arg[]                    $args Arguments
 */
class PHPParser_Node_Expr_FuncCall extends PHPParser_Node_Expr
{
    /**
     * Constructs a function call node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $name       Function name
     * @param PHPParser_Node_Arg[]                    $args       Arguments
     * @param array                                   $attributes Additional attributes
     */
    public function __construct($name, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'name' => $name,
                'args' => $args
            ),
            $attributes
        );
    }
}
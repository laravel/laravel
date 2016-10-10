<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 * @property string|PHPParser_Node_Expr              $name  Method name
 * @property PHPParser_Node_Arg[]                    $args  Arguments
 */
class PHPParser_Node_Expr_StaticCall extends PHPParser_Node_Expr
{
    /**
     * Constructs a static method call node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param string|PHPParser_Node_Expr              $name       Method name
     * @param PHPParser_Node_Arg[]                    $args       Arguments
     * @param array                                   $attributes Additional attributes
     */
    public function __construct($class, $name, array $args = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'class' => $class,
                'name'  => $name,
                'args'  => $args
            ),
            $attributes
        );
    }
}
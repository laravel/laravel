<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 * @property string                                  $name  Constant name
 */
class PHPParser_Node_Expr_ClassConstFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs a class const fetch node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param string                                  $name       Constant name
     * @param array                                   $attributes Additional attributes
     */
    public function __construct($class, $name, array $attributes = array()) {
        parent::__construct(
            array(
                'class' => $class,
                'name'  => $name
            ),
            $attributes
        );
    }
}
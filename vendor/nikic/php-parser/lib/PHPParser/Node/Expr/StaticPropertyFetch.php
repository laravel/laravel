<?php

/**
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 * @property string|PHPParser_Node_Expr              $name  Property name
 */
class PHPParser_Node_Expr_StaticPropertyFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs a static property fetch node.
     *
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param string|PHPParser_Node_Expr              $name       Property name
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
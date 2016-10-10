<?php

/**
 * @property PHPParser_Node_Expr $expr  Expression
 * @property PHPParser_Node_Name|PHPParser_Node_Expr $class Class name
 */
class PHPParser_Node_Expr_Instanceof extends PHPParser_Node_Expr
{
    /**
     * Constructs an instanceof check node.
     *
     * @param PHPParser_Node_Expr                     $expr       Expression
     * @param PHPParser_Node_Name|PHPParser_Node_Expr $class      Class name
     * @param array                                   $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $expr, $class, array $attributes = array()) {
        parent::__construct(
            array(
                'expr'  => $expr,
                'class' => $class
            ),
            $attributes
        );
    }
}
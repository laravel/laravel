<?php

/**
 * @property PHPParser_Node_Expr      $var Variable
 * @property null|PHPParser_Node_Expr $dim Array index / dim
 */
class PHPParser_Node_Expr_ArrayDimFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs an array index fetch node.
     *
     * @param PHPParser_Node_Expr      $var        Variable
     * @param null|PHPParser_Node_Expr $dim        Array index / dim
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $var, PHPParser_Node_Expr $dim = null, array $attributes = array()) {
        parent::__construct(
            array(
                'var' => $var,
                'dim' => $dim
            ),
            $attributes
        );
    }
}
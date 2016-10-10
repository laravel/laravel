<?php

/**
 * @property PHPParser_Node_Expr $var  Variable
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_AssignMinus extends PHPParser_Node_Expr
{
    /**
     * Constructs an assignment with minus node.
     *
     * @param PHPParser_Node_Expr $var        Variable
     * @param PHPParser_Node_Expr $expr       Expression
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $var, PHPParser_Node_Expr $expr, array $attributes = array()) {
        parent::__construct(
            array(
                'var'  => $var,
                'expr' => $expr
            ),
            $attributes
        );
    }
}
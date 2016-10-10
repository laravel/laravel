<?php

/**
 * @property PHPParser_Node_Expr $var  Variable reference is assigned to
 * @property PHPParser_Node_Expr $expr Variable which is referenced
 */
class PHPParser_Node_Expr_AssignRef extends PHPParser_Node_Expr
{
    /**
     * Constructs an assignment node.
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
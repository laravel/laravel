<?php

/**
 * @property PHPParser_Node_Expr      $cond Condition
 * @property null|PHPParser_Node_Expr $if   Expression for true
 * @property PHPParser_Node_Expr      $else Expression for false
 */
class PHPParser_Node_Expr_Ternary extends PHPParser_Node_Expr
{
    /**
     * Constructs a ternary operator node.
     *
     * @param PHPParser_Node_Expr      $cond       Condition
     * @param null|PHPParser_Node_Expr $if         Expression for true
     * @param PHPParser_Node_Expr      $else       Expression for false
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $cond, $if, PHPParser_Node_Expr $else, array $attributes = array()) {
        parent::__construct(
            array(
                'cond' => $cond,
                'if'   => $if,
                'else' => $else
            ),
            $attributes
        );
    }
}
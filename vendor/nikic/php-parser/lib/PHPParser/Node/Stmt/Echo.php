<?php

/**
 * @property PHPParser_Node_Expr[] $exprs Expressions
 */
class PHPParser_Node_Stmt_Echo extends PHPParser_Node_Stmt
{
    /**
     * Constructs an echo node.
     *
     * @param PHPParser_Node_Expr[] $exprs      Expressions
     * @param array                 $attributes Additional attributes
     */
    public function __construct(array $exprs, array $attributes = array()) {
        parent::__construct(
            array(
                'exprs' => $exprs,
            ),
            $attributes
        );
    }
}
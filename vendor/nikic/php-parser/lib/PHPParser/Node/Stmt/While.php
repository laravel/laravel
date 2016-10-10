<?php

/**
 * @property PHPParser_Node_Expr $cond  Condition
 * @property PHPParser_Node[]    $stmts Statements
 */
class PHPParser_Node_Stmt_While extends PHPParser_Node_Stmt
{
    /**
     * Constructs a while node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param PHPParser_Node[]    $stmts      Statements
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $cond, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}
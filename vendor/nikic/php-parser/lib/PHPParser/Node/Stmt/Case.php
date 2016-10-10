<?php

/**
 * @property null|PHPParser_Node_Expr $cond  Condition (null for default)
 * @property PHPParser_Node[]         $stmts Statements
 */
class PHPParser_Node_Stmt_Case extends PHPParser_Node_Stmt
{
    /**
     * Constructs a case node.
     *
     * @param null|PHPParser_Node_Expr $cond       Condition (null for default)
     * @param PHPParser_Node[]         $stmts      Statements
     * @param array                    $attributes Additional attributes
     */
    public function __construct($cond, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}
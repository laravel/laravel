<?php

/**
 * @property PHPParser_Node_Expr        $cond  Condition
 * @property PHPParser_Node_Stmt_Case[] $cases Case list
 */
class PHPParser_Node_Stmt_Switch extends PHPParser_Node_Stmt
{
    /**
     * Constructs a case node.
     *
     * @param PHPParser_Node_Expr        $cond       Condition
     * @param PHPParser_Node_Stmt_Case[] $cases      Case list
     * @param array                      $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $cond, array $cases, array $attributes = array()) {
        parent::__construct(
            array(
                'cond'  => $cond,
                'cases' => $cases,
            ),
            $attributes
        );
    }
}
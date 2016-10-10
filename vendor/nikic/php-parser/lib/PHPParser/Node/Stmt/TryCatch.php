<?php

/**
 * @property PHPParser_Node[]            $stmts        Statements
 * @property PHPParser_Node_Stmt_Catch[] $catches      Catches
 * @property PHPParser_Node[]            $finallyStmts Finally statements
 */
class PHPParser_Node_Stmt_TryCatch extends PHPParser_Node_Stmt
{
    /**
     * Constructs a try catch node.
     *
     * @param PHPParser_Node[]            $stmts        Statements
     * @param PHPParser_Node_Stmt_Catch[] $catches      Catches
     * @param PHPParser_Node[]            $finallyStmts Finally statements (null means no finally clause)
     * @param array|null                  $attributes   Additional attributes
     */
    public function __construct(array $stmts, array $catches, array $finallyStmts = null, array $attributes = array()) {
        if (empty($catches) && null === $finallyStmts) {
            throw new PHPParser_Error('Cannot use try without catch or finally');
        }

        parent::__construct(
            array(
                'stmts'        => $stmts,
                'catches'      => $catches,
                'finallyStmts' => $finallyStmts,
            ),
            $attributes
        );
    }
}
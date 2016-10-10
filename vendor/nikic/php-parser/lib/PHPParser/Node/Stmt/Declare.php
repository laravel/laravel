<?php

/**
 * @property PHPParser_Node_Stmt_DeclareDeclare[] $declares List of declares
 * @property PHPParser_Node[]                     $stmts    Statements
 */
class PHPParser_Node_Stmt_Declare extends PHPParser_Node_Stmt
{
    /**
     * Constructs a declare node.
     *
     * @param PHPParser_Node_Stmt_DeclareDeclare[] $declares   List of declares
     * @param PHPParser_Node[]                     $stmts      Statements
     * @param array                                $attributes Additional attributes
     */
    public function __construct(array $declares, array $stmts, array $attributes = array()) {
        parent::__construct(
            array(
                'declares' => $declares,
                'stmts'    => $stmts,
            ),
            $attributes
        );
    }
}
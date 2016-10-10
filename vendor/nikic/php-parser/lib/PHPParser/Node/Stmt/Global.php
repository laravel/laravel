<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables
 */
class PHPParser_Node_Stmt_Global extends PHPParser_Node_Stmt
{
    /**
     * Constructs a global variables list node.
     *
     * @param PHPParser_Node_Expr[] $vars       Variables to unset
     * @param array                 $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(
            array(
                'vars' => $vars,
            ),
            $attributes
        );
    }
}
<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables to unset
 */
class PHPParser_Node_Stmt_Unset extends PHPParser_Node_Stmt
{
    /**
     * Constructs an unset node.
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
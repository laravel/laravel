<?php

/**
 * @property PHPParser_Node_Expr[] $vars Variables
 */
class PHPParser_Node_Expr_Isset extends PHPParser_Node_Expr
{
    /**
     * Constructs an array node.
     *
     * @param PHPParser_Node_Expr[] $vars       Variables
     * @param array                 $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct(
            array(
                'vars' => $vars
            ),
            $attributes
        );
    }
}
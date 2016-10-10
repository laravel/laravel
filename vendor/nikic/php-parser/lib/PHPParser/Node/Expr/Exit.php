<?php

/**
 * @property null|PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_Exit extends PHPParser_Node_Expr
{
    /**
     * Constructs an exit() node.
     *
     * @param null|PHPParser_Node_Expr $expr       Expression
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $expr = null, array $attributes = array()) {
        parent::__construct(
            array(
                'expr' => $expr
            ),
            $attributes
        );
    }
}
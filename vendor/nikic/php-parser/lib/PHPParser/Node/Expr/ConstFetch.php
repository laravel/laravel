<?php

/**
 * @property PHPParser_Node_Name $name Constant name
 */
class PHPParser_Node_Expr_ConstFetch extends PHPParser_Node_Expr
{
    /**
     * Constructs a const fetch node.
     *
     * @param PHPParser_Node_Name $name       Constant name
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Name $name, array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name
            ),
            $attributes
        );
    }
}
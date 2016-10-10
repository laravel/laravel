<?php

/**
 * @property PHPParser_Node_Expr_ArrayItem[] $items Items
 */
class PHPParser_Node_Expr_Array extends PHPParser_Node_Expr
{
    /**
     * Constructs an array node.
     *
     * @param PHPParser_Node_Expr_ArrayItem[] $items      Items of the array
     * @param array                           $attributes Additional attributes
     */
    public function __construct(array $items = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'items' => $items
            ),
            $attributes
        );
    }
}
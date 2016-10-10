<?php

/**
 * @property PHPParser_Node_Expr      $value Value
 * @property null|PHPParser_Node_Expr $key   Key
 * @property bool                     $byRef Whether to assign by reference
 */
class PHPParser_Node_Expr_ArrayItem extends PHPParser_Node_Expr
{
    /**
     * Constructs an array item node.
     *
     * @param PHPParser_Node_Expr      $value      Value
     * @param null|PHPParser_Node_Expr $key        Key
     * @param bool                     $byRef      Whether to assign by reference
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $value, PHPParser_Node_Expr $key = null, $byRef = false, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
                'byRef' => $byRef
            ),
            $attributes
        );
    }
}
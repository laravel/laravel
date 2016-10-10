<?php

/**
 * @property null|PHPParser_Node_Expr $value Value expression
 * @property null|PHPParser_Node_Expr $key   Key expression
 */
class PHPParser_Node_Expr_Yield extends PHPParser_Node_Expr
{
    /**
     * Constructs a yield expression node.
     *
     * @param null|PHPParser_Node_Expr $value ´    Value expression
     * @param null|PHPParser_Node_Expr $key        Key expression
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $value = null, PHPParser_Node_Expr $key = null, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
            ),
            $attributes
        );
    }
}
<?php

/**
 * @property string              $key   Key
 * @property PHPParser_Node_Expr $value Value
 */
class PHPParser_Node_Stmt_DeclareDeclare extends PHPParser_Node_Stmt
{
    /**
     * Constructs a declare key=>value pair node.
     *
     * @param string              $key        Key
     * @param PHPParser_Node_Expr $value      Value
     * @param array               $attributes Additional attributes
     */
    public function __construct($key, PHPParser_Node_Expr $value, array $attributes = array()) {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
            ),
            $attributes
        );
    }
}
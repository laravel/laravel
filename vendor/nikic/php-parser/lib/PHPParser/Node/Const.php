<?php

/**
 * @property string              $name  Name
 * @property PHPParser_Node_Expr $value Value
 */
class PHPParser_Node_Const extends PHPParser_NodeAbstract
{
    /**
     * Constructs a const node for use in class const and const statements.
     *
     * @param string              $name       Name
     * @param PHPParser_Node_Expr $value      Value
     * @param array               $attributes Additional attributes
     */
    public function __construct($name, PHPParser_Node_Expr $value, array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name,
                'value' => $value,
            ),
            $attributes
        );
    }
}
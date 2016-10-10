<?php

/**
 * @property string                   $name    Name
 * @property null|PHPParser_Node_Expr $default Default
 */
class PHPParser_Node_Stmt_PropertyProperty extends PHPParser_Node_Stmt
{
    /**
     * Constructs a class property node.
     *
     * @param string                   $name       Name
     * @param null|PHPParser_Node_Expr $default    Default value
     * @param array                    $attributes Additional attributes
     */
    public function __construct($name, PHPParser_Node_Expr $default = null, array $attributes = array()) {
        parent::__construct(
            array(
                'name'    => $name,
                'default' => $default,
            ),
            $attributes
        );
    }
}
<?php

/**
 * @property string                   $name    Name
 * @property null|PHPParser_Node_Expr $default Default value
 */
class PHPParser_Node_Stmt_StaticVar extends PHPParser_Node_Stmt
{
    /**
     * Constructs a static variable node.
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
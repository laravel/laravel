<?php

/**
 * @property string           $name  Name
 * @property PHPParser_Node[] $stmts Statements
 */
class PHPParser_Node_Stmt_Trait extends PHPParser_Node_Stmt
{
    /**
     * Constructs a trait node.
     *
     * @param string           $name       Name
     * @param PHPParser_Node[] $stmts      Statements
     * @param array            $attributes Additional attributes
     */
    public function __construct($name, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'name'  => $name,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}
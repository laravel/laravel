<?php

/**
 * @property PHPParser_Node_Name $type  Class of exception
 * @property string              $var   Variable for exception
 * @property PHPParser_Node[]    $stmts Statements
 */
class PHPParser_Node_Stmt_Catch extends PHPParser_Node_Stmt
{
    /**
     * Constructs a catch node.
     *
     * @param PHPParser_Node_Name $type       Class of exception
     * @param string              $var        Variable for exception
     * @param PHPParser_Node[]    $stmts      Statements
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Name $type, $var, array $stmts = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'type'  => $type,
                'var'   => $var,
                'stmts' => $stmts,
            ),
            $attributes
        );
    }
}
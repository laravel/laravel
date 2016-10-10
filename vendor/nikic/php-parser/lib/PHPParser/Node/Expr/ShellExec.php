<?php

/**
 * @property array $parts Encapsed string array
 */
class PHPParser_Node_Expr_ShellExec extends PHPParser_Node_Expr
{
    /**
     * Constructs a shell exec (backtick) node.
     *
     * @param array       $parts      Encapsed string array
     * @param array       $attributes Additional attributes
     */
    public function __construct($parts, array $attributes = array()) {
        parent::__construct(
            array(
                'parts' => $parts
            ),
            $attributes
        );
    }
}
<?php

/**
 * @property PHPParser_Node_Stmt_UseUse[] $uses Aliases
 */
class PHPParser_Node_Stmt_Use extends PHPParser_Node_Stmt
{
    /**
     * Constructs an alias (use) list node.
     *
     * @param PHPParser_Node_Stmt_UseUse[] $uses       Aliases
     * @param array                        $attributes Additional attributes
     */
    public function __construct(array $uses, array $attributes = array()) {
        parent::__construct(
            array(
                'uses' => $uses,
            ),
            $attributes
        );
    }
}
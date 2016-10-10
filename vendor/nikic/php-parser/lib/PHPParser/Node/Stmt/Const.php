<?php

/**
 * @property PHPParser_Node_Const[] $consts Constant declarations
 */
class PHPParser_Node_Stmt_Const extends PHPParser_Node_Stmt
{
    /**
     * Constructs a const list node.
     *
     * @param PHPParser_Node_Const[] $consts     Constant declarations
     * @param array                  $attributes Additional attributes
     */
    public function __construct(array $consts, array $attributes = array()) {
        parent::__construct(
            array(
                'consts' => $consts,
            ),
            $attributes
        );
    }
}
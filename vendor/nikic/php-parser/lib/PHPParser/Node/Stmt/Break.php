<?php

/**
 * @property null|PHPParser_Node_Expr $num Number of loops to break
 */
class PHPParser_Node_Stmt_Break extends PHPParser_Node_Stmt
{
    /**
     * Constructs a break node.
     *
     * @param null|PHPParser_Node_Expr $num        Number of loops to break
     * @param array                    $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $num = null, array $attributes = array()) {
        parent::__construct(
            array(
                'num' => $num,
            ),
            $attributes
        );
    }
}
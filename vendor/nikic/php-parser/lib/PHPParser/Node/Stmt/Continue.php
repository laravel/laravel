<?php

/**
 * @property null|PHPParser_Node_Expr $num Number of loops to continue
 */
class PHPParser_Node_Stmt_Continue extends PHPParser_Node_Stmt
{
    /**
     * Constructs a continue node.
     *
     * @param null|PHPParser_Node_Expr $num        Number of loops to continue
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
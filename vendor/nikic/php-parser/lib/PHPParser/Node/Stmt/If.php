<?php

/**
 * @property PHPParser_Node_Expr           $cond    Condition expression
 * @property PHPParser_Node[]              $stmts   Statements
 * @property PHPParser_Node_Stmt_ElseIf[]  $elseifs Elseif clauses
 * @property null|PHPParser_Node_Stmt_Else $else    Else clause
 */
class PHPParser_Node_Stmt_If extends PHPParser_Node_Stmt
{

    /**
     * Constructs an if node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param array               $subNodes   Array of the following optional subnodes:
     *                                        'stmts'   => array(): Statements
     *                                        'elseifs' => array(): Elseif clauses
     *                                        'else'    => null   : Else clause
     * @param array               $attributes Additional attributes
     */
    public function __construct(PHPParser_Node_Expr $cond, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'stmts'   => array(),
                'elseifs' => array(),
                'else'    => null,
            ),
            $attributes
        );
        $this->cond = $cond;
    }
}
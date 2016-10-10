<?php

/**
 * @property PHPParser_Node_Name   $trait     Trait name
 * @property string                $method    Method name
 * @property PHPParser_Node_Name[] $insteadof Overwritten traits
 */
class PHPParser_Node_Stmt_TraitUseAdaptation_Precedence extends PHPParser_Node_Stmt_TraitUseAdaptation
{
    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param PHPParser_Node_Name   $trait       Trait name
     * @param string                $method      Method name
     * @param PHPParser_Node_Name[] $insteadof   Overwritten traits
     * @param array                 $attributes  Additional attributes
     */
    public function __construct(PHPParser_Node_Name $trait, $method, array $insteadof, array $attributes = array()) {
        parent::__construct(
            array(
                'trait'     => $trait,
                'method'    => $method,
                'insteadof' => $insteadof,
            ),
            $attributes
        );
    }
}
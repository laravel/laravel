<?php

namespace ClassPreloader\Parser;

/**
 * Finds all references to __DIR__ and replaces them with the actual directory
 */
class DirVisitor extends AbstractNodeVisitor
{
    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Scalar_DirConst) {
            return new \PHPParser_Node_Scalar_String($this->getDir());
        }
    }
}

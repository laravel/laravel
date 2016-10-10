<?php

namespace ClassPreloader\Parser;

/**
 * Finds all references to __FILE__ and replaces them with the actual file path
 */
class FileVisitor extends AbstractNodeVisitor
{
    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Scalar_FileConst) {
            return new \PHPParser_Node_Scalar_String($this->getFilename());
        }
    }
}

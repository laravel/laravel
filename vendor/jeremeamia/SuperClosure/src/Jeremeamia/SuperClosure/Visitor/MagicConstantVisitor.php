<?php

namespace Jeremeamia\SuperClosure\Visitor;

use Jeremeamia\SuperClosure\ClosureLocation;
use PHPParser_Node_Scalar_LNumber as NumberNode;
use PHPParser_Node_Scalar_String as StringNode;

/**
 * This is a visitor that resolves magic constants (e.g., __FILE__) to their intended values within a closure's AST
 *
 * @copyright Jeremy Lindblom 2010-2013
 */
class MagicConstantVisitor extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var ClosureLocation
     */
    protected $location;

    /**
     * @param ClosureLocation $location
     */
    public function __construct(ClosureLocation $location)
    {
        $this->location = $location;
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {
            case 'Scalar_LineConst' :
                return new NumberNode($node->getAttribute('startLine'));
            case 'Scalar_FileConst' :
                return new StringNode($this->location->file);
            case 'Scalar_DirConst' :
                return new StringNode($this->location->directory);
            case 'Scalar_FuncConst' :
                return new StringNode($this->location->function);
            case 'Scalar_NSConst' :
                return new StringNode($this->location->namespace);
            case 'Scalar_ClassConst' :
                return new StringNode($this->location->class);
            case 'Scalar_MethodConst' :
                return new StringNode($this->location->method);
            case 'Scalar_TraitConst' :
                return new StringNode($this->location->trait);
        }
    }
}

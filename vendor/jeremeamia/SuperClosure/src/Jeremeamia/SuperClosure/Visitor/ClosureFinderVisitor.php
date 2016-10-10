<?php

namespace Jeremeamia\SuperClosure\Visitor;

use Jeremeamia\SuperClosure\ClosureLocation;

/**
 * This is a visitor that extends the nikic/php-parser library and looks for a closure node and its location
 *
 * @copyright Jeremy Lindblom 2010-2013
 */
class ClosureFinderVisitor extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var \ReflectionFunction
     */
    protected $reflection;

    /**
     * @var \PHPParser_Node_Expr_Closure
     */
    protected $closureNode;

    /**
     * @var ClosureLocation
     */
    protected $location;

    /**
     * @param \ReflectionFunction $reflection
     */
    public function __construct(\ReflectionFunction $reflection)
    {
        $this->reflection = $reflection;
        $this->location = new ClosureLocation;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->location = ClosureLocation::fromReflection($this->reflection);
    }

    public function afterTraverse(array $nodes)
    {
        $this->location->finalize();
    }

    public function enterNode(\PHPParser_Node $node)
    {
        // Determine information about the closure's location
        if (!$this->closureNode) {
            if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
                $this->location->namespace = is_array($node->name->parts) ? implode('\\', $node->name->parts) : null;
            }
            if ($node instanceof \PHPParser_Node_Stmt_Trait) {
                $this->location->trait = $this->location->namespace . '\\' . $node->name;
                $this->location->class = null;
            }
            elseif ($node instanceof \PHPParser_Node_Stmt_Class) {
                $this->location->class = $this->location->namespace . '\\' . $node->name;
                $this->location->trait = null;
            }
        }

        // Locate the node of the closure
        if ($node instanceof \PHPParser_Node_Expr_Closure) {
            if ($node->getAttribute('startLine') == $this->reflection->getStartLine()) {
                if ($this->closureNode) {
                    throw new \RuntimeException('Two closures were declared on the same line of code. Cannot determine '
                        . 'which closure was the intended target.');
                } else {
                    $this->closureNode = $node;
                }
            }
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        // Determine information about the closure's location
        if (!$this->closureNode) {
            if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
                $this->location->namespace = null;
            }
            if ($node instanceof \PHPParser_Node_Stmt_Trait) {
                $this->location->trait = null;
            }
            elseif ($node instanceof \PHPParser_Node_Stmt_Class) {
                $this->location->class = null;
            }
        }
    }

    /**
     * @return \PHPParser_Node_Expr_Closure
     */
    public function getClosureNode()
    {
        return $this->closureNode;
    }

    /**
     * @return ClosureLocation
     */
    public function getLocation()
    {
        return $this->location;
    }
}

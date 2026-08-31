<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Psy\Exception\FatalErrorException;

/**
 * The abstract class pass handles abstract classes and methods, complaining if there are too few or too many of either.
 */
class AbstractClassPass extends CodeCleanerPass
{
    private Class_ $class;
    private array $abstractMethods;

    /**
     * @throws FatalErrorException if the node is an abstract function with a body
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->class = $node;
            $this->abstractMethods = [];
        } elseif ($node instanceof ClassMethod) {
            if ($node->isAbstract()) {
                $name = \sprintf('%s::%s', $this->class->name, $node->name);
                $this->abstractMethods[] = $name;

                if ($node->stmts !== null) {
                    $msg = \sprintf('Abstract function %s cannot contain body', $name);
                    throw new FatalErrorException($msg, 0, \E_ERROR, null, $node->getStartLine());
                }
            }
        }

        return null;
    }

    /**
     * @throws FatalErrorException if the node is a non-abstract class with abstract methods
     *
     * @param Node $node
     *
     * @return int|Node|Node[]|null Replacement node (or special return value)
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_) {
            $count = \count($this->abstractMethods);
            if ($count > 0 && !$node->isAbstract()) {
                $msg = \sprintf(
                    'Class %s contains %d abstract method%s must therefore be declared abstract or implement the remaining methods (%s)',
                    $node->name,
                    $count,
                    ($count === 1) ? '' : 's',
                    \implode(', ', $this->abstractMethods)
                );
                throw new FatalErrorException($msg, 0, \E_ERROR, null, $node->getStartLine());
            }
        }

        return null;
    }
}

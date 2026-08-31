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
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Stmt\Foreach_;
use Psy\Exception\FatalErrorException;

/**
 * Validate empty brackets are only used for assignment.
 */
class EmptyArrayDimFetchPass extends CodeCleanerPass
{
    const EXCEPTION_MESSAGE = 'Cannot use [] for reading';

    private array $theseOnesAreFine = [];

    /**
     * @return Node[]|null Array of nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $this->theseOnesAreFine = [];

        return null;
    }

    /**
     * @throws FatalErrorException if the user used empty array dim fetch outside of assignment
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Assign && $node->var instanceof ArrayDimFetch) {
            $this->theseOnesAreFine[] = $node->var;
        } elseif ($node instanceof AssignRef && $node->expr instanceof ArrayDimFetch) {
            $this->theseOnesAreFine[] = $node->expr;
        } elseif ($node instanceof Foreach_ && $node->valueVar instanceof ArrayDimFetch) {
            $this->theseOnesAreFine[] = $node->valueVar;
        } elseif ($node instanceof ArrayDimFetch && $node->var instanceof ArrayDimFetch) {
            // $a[]['b'] = 'c'
            if (\in_array($node, $this->theseOnesAreFine)) {
                $this->theseOnesAreFine[] = $node->var;
            }
        }

        if ($node instanceof ArrayDimFetch && $node->dim === null) {
            if (!\in_array($node, $this->theseOnesAreFine)) {
                throw new FatalErrorException(self::EXCEPTION_MESSAGE, $node->getStartLine());
            }
        }

        return null;
    }
}

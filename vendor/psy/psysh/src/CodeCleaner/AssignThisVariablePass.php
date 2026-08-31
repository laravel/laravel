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
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use Psy\Exception\FatalErrorException;

/**
 * Validate that the user input does not assign the `$this` variable.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class AssignThisVariablePass extends CodeCleanerPass
{
    /**
     * Validate that the user input does not assign the `$this` variable.
     *
     * @throws FatalErrorException if the user assign the `$this` variable
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Assign && $node->var instanceof Variable && $node->var->name === 'this') {
            throw new FatalErrorException('Cannot re-assign $this', 0, \E_ERROR, null, $node->getStartLine());
        }

        return null;
    }
}

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
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\While_;
use Psy\Exception\FatalErrorException;

/**
 * Validate that function calls will succeed.
 *
 * This pass throws a FatalErrorException rather than letting PHP run
 * headfirst into a real fatal error and die.
 */
class ValidFunctionNamePass extends NamespaceAwarePass
{
    private int $conditionalScopes = 0;

    /**
     * Store newly defined function names on the way in, to allow recursion.
     *
     * @throws FatalErrorException if a function is redefined in a non-conditional scope
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if (self::isConditional($node)) {
            $this->conditionalScopes++;
        } elseif ($node instanceof Function_) {
            $name = $this->getFullyQualifiedName($node->name);

            // @todo add an "else" here which adds a runtime check for instances where we can't tell
            // whether a function is being redefined by static analysis alone.
            if ($this->conditionalScopes === 0) {
                if (\function_exists($name) ||
                    isset($this->currentScope[\strtolower($name)])) {
                    $msg = \sprintf('Cannot redeclare %s()', $name);
                    throw new FatalErrorException($msg, 0, \E_ERROR, null, $node->getStartLine());
                }
            }

            $this->currentScope[\strtolower($name)] = true;
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return int|Node|Node[]|null Replacement node (or special return value)
     */
    public function leaveNode(Node $node)
    {
        if (self::isConditional($node)) {
            $this->conditionalScopes--;
        }

        return null;
    }

    private static function isConditional(Node $node)
    {
        return $node instanceof If_ ||
            $node instanceof While_ ||
            $node instanceof Do_ ||
            $node instanceof Switch_;
    }
}

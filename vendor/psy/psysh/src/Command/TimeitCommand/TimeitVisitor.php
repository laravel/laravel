<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\TimeitCommand;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Name\FullyQualified as FullyQualifiedName;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;
use Psy\CodeCleaner\NoReturnValue;
use Psy\Command\TimeitCommand;

/**
 * A node visitor for instrumenting code to be executed by the `timeit` command.
 *
 * Injects `TimeitCommand::markStart()` at the start of code to be executed, and
 * `TimeitCommand::markEnd()` at the end, and on top-level return statements.
 */
class TimeitVisitor extends NodeVisitorAbstract
{
    private int $functionDepth = 0;

    /**
     * {@inheritdoc}
     *
     * @return Node[]|null Array of nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $this->functionDepth = 0;

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        // keep track of nested function-like nodes, because they can have
        // returns statements... and we don't want to call markEnd for those.
        if ($node instanceof FunctionLike) {
            $this->functionDepth++;

            return null;
        }

        // replace any top-level `return` statements with a `markEnd` call
        if ($this->functionDepth === 0 && $node instanceof Return_) {
            return new Return_($this->getEndCall($node->expr), $node->getAttributes());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|Node|Node[]|null Replacement node (or special return value)
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof FunctionLike) {
            $this->functionDepth--;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return Node[]|null Array of nodes
     */
    public function afterTraverse(array $nodes)
    {
        // prepend a `markStart` call
        \array_unshift($nodes, new Expression($this->getStartCall(), []));

        // append a `markEnd` call (wrapping the final node, if it's an expression)
        $last = $nodes[\count($nodes) - 1];
        if ($last instanceof Expr) {
            \array_pop($nodes);
            $nodes[] = $this->getEndCall($last);
        } elseif ($last instanceof Expression) {
            \array_pop($nodes);
            $nodes[] = new Expression($this->getEndCall($last->expr), $last->getAttributes());
        } elseif ($last instanceof Return_) {
            // nothing to do here, we're already ending with a return call
        } else {
            $nodes[] = new Expression($this->getEndCall(), []);
        }

        return $nodes;
    }

    /**
     * Get PhpParser AST nodes for a `markStart` call.
     *
     * @return \PhpParser\Node\Expr\StaticCall
     */
    private function getStartCall(): StaticCall
    {
        return new StaticCall(new FullyQualifiedName(TimeitCommand::class), 'markStart');
    }

    /**
     * Get PhpParser AST nodes for a `markEnd` call.
     *
     * Optionally pass in a return value.
     *
     * @param Expr|null $arg
     */
    private function getEndCall(?Expr $arg = null): StaticCall
    {
        if ($arg === null) {
            $arg = NoReturnValue::create();
        }

        return new StaticCall(new FullyQualifiedName(TimeitCommand::class), 'markEnd', [new Arg($arg)]);
    }
}

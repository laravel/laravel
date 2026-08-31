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
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;

/**
 * Add an implicit "return" to the last statement, provided it can be returned.
 */
class ImplicitReturnPass extends CodeCleanerPass
{
    /**
     * @param array $nodes
     *
     * @return array
     */
    public function beforeTraverse(array $nodes): array
    {
        return $this->addImplicitReturn($nodes);
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    private function addImplicitReturn(array $nodes): array
    {
        // If nodes is empty, it can't have a return value.
        if (empty($nodes)) {
            return [new Return_(NoReturnValue::create())];
        }

        $last = \end($nodes);

        // Special case a few types of statements to add an implicit return
        // value (even though they technically don't have any return value)
        // because showing a return value in these instances is useful and not
        // very surprising.
        if ($last instanceof If_) {
            $last->stmts = $this->addImplicitReturn($last->stmts);

            foreach ($last->elseifs as $elseif) {
                $elseif->stmts = $this->addImplicitReturn($elseif->stmts);
            }

            if ($last->else) {
                $last->else->stmts = $this->addImplicitReturn($last->else->stmts);
            }
        } elseif ($last instanceof Switch_) {
            foreach ($last->cases as $case) {
                // only add an implicit return to cases which end in break
                $caseLast = \end($case->stmts);
                if ($caseLast instanceof Break_) {
                    $case->stmts = $this->addImplicitReturn(\array_slice($case->stmts, 0, -1));
                    $case->stmts[] = $caseLast;
                }
            }
        } elseif ($last instanceof Expr && !($last instanceof Exit_) && !self::isThrowNode($last)) {
            // @codeCoverageIgnoreStart
            $nodes[\count($nodes) - 1] = new Return_($last, [
                'startLine' => $last->getStartLine(),
                'endLine'   => $last->getEndLine(),
            ]);
            // @codeCoverageIgnoreEnd
        } elseif ($last instanceof Expression && !($last->expr instanceof Exit_) && !self::isThrowNode($last->expr)) {
            $nodes[\count($nodes) - 1] = new Return_($last->expr, [
                'startLine' => $last->getStartLine(),
                'endLine'   => $last->getEndLine(),
            ]);
        } elseif ($last instanceof Namespace_) {
            $last->stmts = $this->addImplicitReturn($last->stmts);
        }

        // Return a "no return value" for all non-expression statements, so that
        // PsySH can suppress the `null` that `eval()` returns otherwise.
        //
        // Note that statements special cased above (if/elseif/else, switch)
        // _might_ implicitly return a value before this catch-all return is
        // reached.
        //
        // We're not adding a fallback return after namespace statements,
        // because code outside namespace statements doesn't really work, and
        // there's already an implicit return in the namespace statement anyway.
        if (self::isNonExpressionStmt($last) && !self::isThrowNode($last)) {
            $nodes[] = new Return_(NoReturnValue::create());
        }

        return $nodes;
    }

    /**
     * Check whether a given node is a non-expression statement.
     *
     * As of PHP Parser 4.x, Expressions are now instances of Stmt as well, so
     * we'll exclude them here.
     *
     * @param Node $node
     */
    private static function isNonExpressionStmt(Node $node): bool
    {
        return $node instanceof Stmt &&
            !$node instanceof Expression &&
            !$node instanceof Return_ &&
            !$node instanceof Namespace_;
    }

    /**
     * PHP-Parser 4.x modeled standalone `throw` as Stmt\Throw_, while newer
     * versions expose it as Expr\Throw_ inside Stmt\Expression.
     */
    private static function isThrowNode(Node $node): bool
    {
        return $node instanceof Throw_ || $node->getType() === 'Stmt_Throw';
    }
}

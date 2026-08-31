<?php declare(strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeVisitorAbstract;

final class CyclomaticComplexityCalculatingVisitor extends NodeVisitorAbstract
{
    /**
     * @var positive-int
     */
    private int $cyclomaticComplexity = 1;

    public function enterNode(Node $node): void
    {
        switch ($node::class) {
            case BooleanAnd::class:
            case BooleanOr::class:
            case Case_::class:
            case Catch_::class:
            case ElseIf_::class:
            case For_::class:
            case Foreach_::class:
            case If_::class:
            case LogicalAnd::class:
            case LogicalOr::class:
            case Node\MatchArm::class:
            case Ternary::class:
            case While_::class:
                $this->cyclomaticComplexity++;
        }
    }

    /**
     * @return positive-int
     */
    public function cyclomaticComplexity(): int
    {
        return $this->cyclomaticComplexity;
    }
}

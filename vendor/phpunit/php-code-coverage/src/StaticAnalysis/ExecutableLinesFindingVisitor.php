<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use function array_diff_key;
use function assert;
use function count;
use function current;
use function end;
use function explode;
use function max;
use function preg_match;
use function preg_quote;
use function range;
use function reset;
use function sprintf;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type LinesType from \SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser
 */
final class ExecutableLinesFindingVisitor extends NodeVisitorAbstract
{
    private int $nextBranch = 0;
    private readonly string $source;

    /**
     * @var LinesType
     */
    private array $executableLinesGroupedByBranch = [];

    /**
     * @var array<int, bool>
     */
    private array $unsets = [];

    /**
     * @var array<int, string>
     */
    private array $commentsToCheckForUnset = [];

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function enterNode(Node $node): void
    {
        foreach ($node->getComments() as $comment) {
            $commentLine = $comment->getStartLine();

            if (!isset($this->executableLinesGroupedByBranch[$commentLine])) {
                continue;
            }

            foreach (explode("\n", $comment->getText()) as $text) {
                $this->commentsToCheckForUnset[$commentLine] = $text;
                $commentLine++;
            }
        }

        if ($node instanceof Node\Scalar\String_ ||
            $node instanceof Node\Scalar\EncapsedStringPart) {
            $startLine = $node->getStartLine() + 1;
            $endLine   = $node->getEndLine() - 1;

            if ($startLine <= $endLine) {
                foreach (range($startLine, $endLine) as $line) {
                    unset($this->executableLinesGroupedByBranch[$line]);
                }
            }

            return;
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach (range($node->getStartLine(), $node->getEndLine()) as $line) {
                $this->unsets[$line] = true;
            }

            return;
        }

        if ($node instanceof Node\Stmt\Declare_ ||
            $node instanceof Node\Stmt\DeclareDeclare ||
            $node instanceof Node\Stmt\Else_ ||
            $node instanceof Node\Stmt\EnumCase ||
            $node instanceof Node\Stmt\Finally_ ||
            $node instanceof Node\Stmt\GroupUse ||
            $node instanceof Node\Stmt\Label ||
            $node instanceof Node\Stmt\Namespace_ ||
            $node instanceof Node\Stmt\Nop ||
            $node instanceof Node\Stmt\Switch_ ||
            $node instanceof Node\Stmt\TryCatch ||
            $node instanceof Node\Stmt\Use_ ||
            $node instanceof Node\Stmt\UseUse ||
            $node instanceof Node\Expr\ConstFetch ||
            $node instanceof Node\Expr\Variable ||
            $node instanceof Node\Expr\Throw_ ||
            $node instanceof Node\ComplexType ||
            $node instanceof Node\Const_ ||
            $node instanceof Node\Identifier ||
            $node instanceof Node\Name ||
            $node instanceof Node\Param ||
            $node instanceof Node\Scalar) {
            return;
        }

        if ($node instanceof Node\Expr\Match_) {
            foreach ($node->arms as $arm) {
                $this->setLineBranch(
                    $arm->body->getStartLine(),
                    $arm->body->getEndLine(),
                    ++$this->nextBranch,
                );
            }

            return;
        }

        if ($node instanceof Node\Stmt\Expression && $node->expr instanceof Node\Expr\Throw_) {
            $this->setLineBranch($node->expr->expr->getEndLine(), $node->expr->expr->getEndLine(), ++$this->nextBranch);

            return;
        }

        if ($node instanceof Node\Stmt\Enum_ ||
            $node instanceof Node\Stmt\Function_ ||
            $node instanceof Node\Stmt\Class_ ||
            $node instanceof Node\Stmt\ClassMethod ||
            $node instanceof Node\Expr\Closure ||
            $node instanceof Node\Stmt\Trait_) {
            if ($node instanceof Node\Stmt\Function_ || $node instanceof Node\Stmt\ClassMethod) {
                $unsets = [];

                foreach ($node->getParams() as $param) {
                    foreach (range($param->getStartLine(), $param->getEndLine()) as $line) {
                        $unsets[$line] = true;
                    }
                }

                unset($unsets[$node->getEndLine()]);

                $this->unsets += $unsets;
            }

            $isConcreteClassLike = $node instanceof Node\Stmt\Enum_ || $node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_;

            if (null !== $node->stmts) {
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\Nop) {
                        continue;
                    }

                    foreach (range($stmt->getStartLine(), $stmt->getEndLine()) as $line) {
                        unset($this->executableLinesGroupedByBranch[$line]);

                        if (
                            $isConcreteClassLike &&
                            !$stmt instanceof Node\Stmt\ClassMethod
                        ) {
                            $this->unsets[$line] = true;
                        }
                    }
                }
            }

            if ($isConcreteClassLike) {
                return;
            }

            $hasEmptyBody = [] === $node->stmts ||
                null === $node->stmts ||
                (
                    1 === count($node->stmts) &&
                    $node->stmts[0] instanceof Node\Stmt\Nop
                );

            if ($hasEmptyBody) {
                if ($node->getEndLine() === $node->getStartLine() && isset($this->executableLinesGroupedByBranch[$node->getStartLine()])) {
                    return;
                }

                $this->setLineBranch($node->getEndLine(), $node->getEndLine(), ++$this->nextBranch);

                return;
            }

            return;
        }

        if ($node instanceof Node\Expr\ArrowFunction) {
            $startLine = max(
                $node->getStartLine() + 1,
                $node->expr->getStartLine(),
            );

            $endLine = $node->expr->getEndLine();

            if ($endLine < $startLine) {
                return;
            }

            $this->setLineBranch($startLine, $endLine, ++$this->nextBranch);

            return;
        }

        if ($node instanceof Node\Expr\Ternary) {
            if (null !== $node->if &&
                $node->getStartLine() !== $node->if->getEndLine()) {
                $this->setLineBranch($node->if->getStartLine(), $node->if->getEndLine(), ++$this->nextBranch);
            }

            if ($node->getStartLine() !== $node->else->getEndLine()) {
                $this->setLineBranch($node->else->getStartLine(), $node->else->getEndLine(), ++$this->nextBranch);
            }

            return;
        }

        if ($node instanceof Node\Expr\BinaryOp\Coalesce) {
            if ($node->getStartLine() !== $node->getEndLine()) {
                $this->setLineBranch($node->getEndLine(), $node->getEndLine(), ++$this->nextBranch);
            }

            return;
        }

        if ($node instanceof Node\Stmt\If_ ||
            $node instanceof Node\Stmt\ElseIf_ ||
            $node instanceof Node\Stmt\Case_) {
            if (null === $node->cond) {
                return;
            }

            $this->setLineBranch(
                $node->cond->getStartLine(),
                $node->cond->getStartLine(),
                ++$this->nextBranch,
            );

            return;
        }

        if ($node instanceof Node\Stmt\For_) {
            $startLine = null;
            $endLine   = null;

            if ([] !== $node->init) {
                $startLine = $node->init[0]->getStartLine();

                end($node->init);

                $endLine = current($node->init)->getEndLine();

                reset($node->init);
            }

            if ([] !== $node->cond) {
                if (null === $startLine) {
                    $startLine = $node->cond[0]->getStartLine();
                }

                end($node->cond);

                $endLine = current($node->cond)->getEndLine();

                reset($node->cond);
            }

            if ([] !== $node->loop) {
                if (null === $startLine) {
                    $startLine = $node->loop[0]->getStartLine();
                }

                end($node->loop);

                $endLine = current($node->loop)->getEndLine();

                reset($node->loop);
            }

            if (null === $startLine || null === $endLine) {
                return;
            }

            $this->setLineBranch(
                $startLine,
                $endLine,
                ++$this->nextBranch,
            );

            return;
        }

        if ($node instanceof Node\Stmt\Foreach_) {
            $this->setLineBranch(
                $node->expr->getStartLine(),
                $node->valueVar->getEndLine(),
                ++$this->nextBranch,
            );

            return;
        }

        if ($node instanceof Node\Stmt\While_ ||
            $node instanceof Node\Stmt\Do_) {
            $this->setLineBranch(
                $node->cond->getStartLine(),
                $node->cond->getEndLine(),
                ++$this->nextBranch,
            );

            return;
        }

        if ($node instanceof Node\Stmt\Catch_) {
            assert([] !== $node->types);
            $startLine = $node->types[0]->getStartLine();
            end($node->types);
            $endLine = current($node->types)->getEndLine();

            $this->setLineBranch(
                $startLine,
                $endLine,
                ++$this->nextBranch,
            );

            return;
        }

        if ($node instanceof Node\Expr\CallLike) {
            if (isset($this->executableLinesGroupedByBranch[$node->getStartLine()])) {
                $branch = $this->executableLinesGroupedByBranch[$node->getStartLine()];
            } else {
                $branch = ++$this->nextBranch;
            }

            $this->setLineBranch($node->getStartLine(), $node->getEndLine(), $branch);

            return;
        }

        if (isset($this->executableLinesGroupedByBranch[$node->getStartLine()])) {
            return;
        }

        $this->setLineBranch($node->getStartLine(), $node->getEndLine(), ++$this->nextBranch);
    }

    public function afterTraverse(array $nodes): void
    {
        $lines = explode("\n", $this->source);

        foreach ($lines as $lineNumber => $line) {
            $lineNumber++;

            if (1 === preg_match('/^\s*$/', $line) ||
                (
                    isset($this->commentsToCheckForUnset[$lineNumber]) &&
                    1 === preg_match(sprintf('/^\s*%s\s*$/', preg_quote($this->commentsToCheckForUnset[$lineNumber], '/')), $line)
                )) {
                unset($this->executableLinesGroupedByBranch[$lineNumber]);
            }
        }

        $this->executableLinesGroupedByBranch = array_diff_key(
            $this->executableLinesGroupedByBranch,
            $this->unsets,
        );
    }

    /**
     * @return LinesType
     */
    public function executableLinesGroupedByBranch(): array
    {
        return $this->executableLinesGroupedByBranch;
    }

    private function setLineBranch(int $start, int $end, int $branch): void
    {
        foreach (range($start, $end) as $line) {
            $this->executableLinesGroupedByBranch[$line] = $branch;
        }
    }
}

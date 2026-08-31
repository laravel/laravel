<?php declare(strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\LinesOfCode;

use function array_merge;
use function array_unique;
use function assert;
use function count;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

final class LineCountingVisitor extends NodeVisitorAbstract
{
    /**
     * @var non-negative-int
     */
    private readonly int $linesOfCode;

    /**
     * @var Comment[]
     */
    private array $comments = [];

    /**
     * @var int[]
     */
    private array $linesWithStatements = [];

    /**
     * @param non-negative-int $linesOfCode
     */
    public function __construct(int $linesOfCode)
    {
        $this->linesOfCode = $linesOfCode;
    }

    public function enterNode(Node $node): void
    {
        $this->comments = array_merge($this->comments, $node->getComments());

        if (!$node instanceof Expr) {
            return;
        }

        $this->linesWithStatements[] = $node->getStartLine();
    }

    public function result(): LinesOfCode
    {
        $commentLinesOfCode = 0;

        foreach ($this->comments() as $comment) {
            $commentLinesOfCode += ($comment->getEndLine() - $comment->getStartLine() + 1);
        }

        $nonCommentLinesOfCode = $this->linesOfCode - $commentLinesOfCode;
        $logicalLinesOfCode    = count(array_unique($this->linesWithStatements));

        assert($commentLinesOfCode >= 0);
        assert($nonCommentLinesOfCode >= 0);

        return new LinesOfCode(
            $this->linesOfCode,
            $commentLinesOfCode,
            $nonCommentLinesOfCode,
            $logicalLinesOfCode,
        );
    }

    /**
     * @return Comment[]
     */
    private function comments(): array
    {
        $comments = [];

        foreach ($this->comments as $comment) {
            $comments[$comment->getStartLine() . '_' . $comment->getStartTokenPos() . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos()] = $comment;
        }

        return $comments;
    }
}

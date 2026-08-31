<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Token;

class CommentAnnotatingVisitor extends NodeVisitorAbstract {
    /** @var int Last seen token start position */
    private int $pos = 0;
    /** @var Token[] Token array */
    private array $tokens;
    /** @var list<int> Token positions of comments */
    private array $commentPositions = [];

    /**
     * Create a comment annotation visitor.
     *
     * @param Token[] $tokens Token array
     */
    public function __construct(array $tokens) {
        $this->tokens = $tokens;

        // Collect positions of comments. We use this to avoid traversing parts of the AST where
        // there are no comments.
        foreach ($tokens as $i => $token) {
            if ($token->id === \T_COMMENT || $token->id === \T_DOC_COMMENT) {
                $this->commentPositions[] = $i;
            }
        }
    }

    public function enterNode(Node $node) {
        $nextCommentPos = current($this->commentPositions);
        if ($nextCommentPos === false) {
            // No more comments.
            return self::STOP_TRAVERSAL;
        }

        $oldPos = $this->pos;
        $this->pos = $pos = $node->getStartTokenPos();
        if ($nextCommentPos > $oldPos && $nextCommentPos < $pos) {
            $comments = [];
            while (--$pos >= $oldPos) {
                $token = $this->tokens[$pos];
                if ($token->id === \T_DOC_COMMENT) {
                    $comments[] = new Comment\Doc(
                        $token->text, $token->line, $token->pos, $pos,
                        $token->getEndLine(), $token->getEndPos() - 1, $pos);
                    continue;
                }
                if ($token->id === \T_COMMENT) {
                    $comments[] = new Comment(
                        $token->text, $token->line, $token->pos, $pos,
                        $token->getEndLine(), $token->getEndPos() - 1, $pos);
                    continue;
                }
                if ($token->id !== \T_WHITESPACE) {
                    break;
                }
            }
            if (!empty($comments)) {
                $node->setAttribute('comments', array_reverse($comments));
            }

            do {
                $nextCommentPos = next($this->commentPositions);
            } while ($nextCommentPos !== false && $nextCommentPos < $this->pos);
        }

        $endPos = $node->getEndTokenPos();
        if ($nextCommentPos > $endPos) {
            // Skip children if there are no comments located inside this node.
            $this->pos = $endPos;
            return self::DONT_TRAVERSE_CHILDREN;
        }

        return null;
    }
}

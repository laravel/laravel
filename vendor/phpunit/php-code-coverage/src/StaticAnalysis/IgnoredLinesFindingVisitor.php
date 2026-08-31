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

use function assert;
use function str_contains;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class IgnoredLinesFindingVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<int>
     */
    private array $ignoredLines = [];
    private readonly bool $useAnnotationsForIgnoringCode;
    private readonly bool $ignoreDeprecated;

    public function __construct(bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecated)
    {
        $this->useAnnotationsForIgnoringCode = $useAnnotationsForIgnoringCode;
        $this->ignoreDeprecated              = $ignoreDeprecated;
    }

    public function enterNode(Node $node): void
    {
        if (!$node instanceof Class_ &&
            !$node instanceof Trait_ &&
            !$node instanceof Interface_ &&
            !$node instanceof Enum_ &&
            !$node instanceof ClassMethod &&
            !$node instanceof Function_ &&
            !$node instanceof Attribute) {
            return;
        }

        if ($node instanceof Class_ && $node->isAnonymous()) {
            return;
        }

        if ($node instanceof Class_ ||
            $node instanceof Trait_ ||
            $node instanceof Interface_ ||
            $node instanceof Attribute) {
            $this->ignoredLines[] = $node->getStartLine();

            assert($node->name !== null);

            // Workaround for https://github.com/nikic/PHP-Parser/issues/886
            $this->ignoredLines[] = $node->name->getStartLine();
        }

        if (!$this->useAnnotationsForIgnoringCode) {
            return;
        }

        if ($node instanceof Interface_) {
            return;
        }

        if ($node instanceof Attribute &&
            $node->name->toString() === 'PHPUnit\Framework\Attributes\CodeCoverageIgnore') {
            $attributeGroup = $node->getAttribute('parent');
            $attributedNode = $attributeGroup->getAttribute('parent');

            for ($line = $attributedNode->getStartLine(); $line <= $attributedNode->getEndLine(); $line++) {
                $this->ignoredLines[] = $line;
            }

            return;
        }

        $this->processDocComment($node);
    }

    /**
     * @return array<int>
     */
    public function ignoredLines(): array
    {
        return $this->ignoredLines;
    }

    private function processDocComment(Node $node): void
    {
        $docComment = $node->getDocComment();

        if ($docComment === null) {
            return;
        }

        if (str_contains($docComment->getText(), '@codeCoverageIgnore')) {
            for ($line = $node->getStartLine(); $line <= $node->getEndLine(); $line++) {
                $this->ignoredLines[] = $line;
            }
        }

        if ($this->ignoreDeprecated && str_contains($docComment->getText(), '@deprecated')) {
            for ($line = $node->getStartLine(); $line <= $node->getEndLine(); $line++) {
                $this->ignoredLines[] = $line;
            }
        }
    }
}

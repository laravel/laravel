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

use function assert;
use function file_get_contents;
use function substr_count;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class Counter
{
    /**
     * @throws RuntimeException
     */
    public function countInSourceFile(string $sourceFile): LinesOfCode
    {
        $source = file_get_contents($sourceFile);

        assert($source !== false);

        return $this->countInSourceString($source);
    }

    /**
     * @throws RuntimeException
     */
    public function countInSourceString(string $source): LinesOfCode
    {
        $linesOfCode = substr_count($source, "\n");

        if ($linesOfCode === 0 && !empty($source)) {
            $linesOfCode = 1;
        }

        try {
            $nodes = (new ParserFactory)->createForHostVersion()->parse($source);

            assert($nodes !== null);

            return $this->countInAbstractSyntaxTree($linesOfCode, $nodes);

            // @codeCoverageIgnoreStart
        } catch (Error $error) {
            throw new RuntimeException(
                $error->getMessage(),
                $error->getCode(),
                $error,
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param non-negative-int $linesOfCode
     * @param Node[]           $nodes
     *
     * @throws RuntimeException
     */
    public function countInAbstractSyntaxTree(int $linesOfCode, array $nodes): LinesOfCode
    {
        $traverser = new NodeTraverser;
        $visitor   = new LineCountingVisitor($linesOfCode);

        $traverser->addVisitor($visitor);

        try {
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            // @codeCoverageIgnoreStart
        } catch (Error $error) {
            throw new RuntimeException(
                $error->getMessage(),
                $error->getCode(),
                $error,
            );
        }
        // @codeCoverageIgnoreEnd

        return $visitor->result();
    }
}

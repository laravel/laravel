<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Xml;

use function assert;
use DOMElement;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type TestType from \SebastianBergmann\CodeCoverage\CodeCoverage
 */
final class Tests
{
    private readonly DOMElement $contextNode;

    public function __construct(DOMElement $context)
    {
        $this->contextNode = $context;
    }

    /**
     * @param TestType $result
     */
    public function addTest(string $test, array $result): void
    {
        $node = $this->contextNode->appendChild(
            $this->contextNode->ownerDocument->createElementNS(
                'https://schema.phpunit.de/coverage/1.0',
                'test',
            ),
        );

        assert($node instanceof DOMElement);

        $node->setAttribute('name', $test);
        $node->setAttribute('size', $result['size']);
        $node->setAttribute('status', $result['status']);
    }
}

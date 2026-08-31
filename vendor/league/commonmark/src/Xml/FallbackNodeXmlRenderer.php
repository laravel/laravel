<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Xml;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Node;

/**
 * @internal
 */
final class FallbackNodeXmlRenderer implements XmlNodeRendererInterface
{
    /**
     * @var array<string, string>
     *
     * @psalm-allow-private-mutation
     */
    private array $classCache = [];

    /**
     * @psalm-allow-private-mutation
     */
    public function getXmlTagName(Node $node): string
    {
        $className = \get_class($node);
        if (isset($this->classCache[$className])) {
            return $this->classCache[$className];
        }

        $type      = $node instanceof AbstractBlock ? 'block' : 'inline';
        $shortName = \strtolower((new \ReflectionClass($node))->getShortName());

        return $this->classCache[$className] = \sprintf('custom_%s_%s', $type, $shortName);
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        $attrs = [];
        foreach ($node->data->export() as $k => $v) {
            if (self::isValueUsable($v)) {
                $attrs[$k] = $v;
            }
        }

        $reflClass = new \ReflectionClass($node);
        foreach ($reflClass->getProperties() as $property) {
            if (\in_array($property->getDeclaringClass()->getName(), [Node::class, AbstractBlock::class, AbstractInline::class], true)) {
                continue;
            }

            $property->setAccessible(true);
            $value = $property->getValue($node);
            if (self::isValueUsable($value)) {
                $attrs[$property->getName()] = $value;
            }
        }

        return $attrs;
    }

    /**
     * @param mixed $var
     *
     * @psalm-pure
     */
    private static function isValueUsable($var): bool
    {
        return \is_string($var) || \is_int($var) || \is_float($var) || \is_bool($var);
    }
}

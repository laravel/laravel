<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Node;

/**
 * Represents a "<selector>:is(<subSelectorList>)" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Hubert Lenoir <lenoir.hubert@gmail.com>
 *
 * @internal
 */
class MatchingNode extends AbstractNode
{
    /**
     * @param array<NodeInterface> $arguments
     */
    public function __construct(
        public readonly NodeInterface $selector,
        public readonly array $arguments = [],
    ) {
    }

    public function getSpecificity(): Specificity
    {
        $argumentsSpecificity = array_reduce(
            $this->arguments,
            static fn ($c, $n) => 1 === $n->getSpecificity()->compareTo($c) ? $n->getSpecificity() : $c,
            new Specificity(0, 0, 0),
        );

        return $this->selector->getSpecificity()->plus($argumentsSpecificity);
    }

    public function __toString(): string
    {
        $selectorArguments = array_map(
            static fn ($n): string => ltrim((string) $n, '*'),
            $this->arguments,
        );

        return \sprintf('%s[%s:is(%s)]', $this->getNodeName(), $this->selector, implode(', ', $selectorArguments));
    }
}

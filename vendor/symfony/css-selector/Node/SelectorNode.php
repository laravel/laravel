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
 * Represents a "<selector>(::|:)<pseudoElement>" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class SelectorNode extends AbstractNode
{
    private ?string $pseudoElement;

    public function __construct(
        private NodeInterface $tree,
        ?string $pseudoElement = null,
    ) {
        $this->pseudoElement = $pseudoElement ? strtolower($pseudoElement) : null;
    }

    public function getTree(): NodeInterface
    {
        return $this->tree;
    }

    public function getPseudoElement(): ?string
    {
        return $this->pseudoElement;
    }

    public function getSpecificity(): Specificity
    {
        return $this->tree->getSpecificity()->plus(new Specificity(0, 0, $this->pseudoElement ? 1 : 0));
    }

    public function __toString(): string
    {
        return \sprintf('%s[%s%s]', $this->getNodeName(), $this->tree, $this->pseudoElement ? '::'.$this->pseudoElement : '');
    }
}

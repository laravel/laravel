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
 * Represents a "<selector>.<name>" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class ClassNode extends AbstractNode
{
    public function __construct(
        private NodeInterface $selector,
        private string $name,
    ) {
    }

    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }

    public function __toString(): string
    {
        return \sprintf('%s[%s.%s]', $this->getNodeName(), $this->selector, $this->name);
    }
}

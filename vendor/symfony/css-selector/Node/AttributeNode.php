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
 * Represents a "<selector>[<namespace>|<attribute> <operator> <value>]" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class AttributeNode extends AbstractNode
{
    public function __construct(
        private NodeInterface $selector,
        private ?string $namespace,
        private string $attribute,
        private string $operator,
        private ?string $value,
    ) {
    }

    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }

    public function __toString(): string
    {
        $attribute = $this->namespace ? $this->namespace.'|'.$this->attribute : $this->attribute;

        return 'exists' === $this->operator
            ? \sprintf('%s[%s[%s]]', $this->getNodeName(), $this->selector, $attribute)
            : \sprintf("%s[%s[%s %s '%s']]", $this->getNodeName(), $this->selector, $attribute, $this->operator, $this->value);
    }
}

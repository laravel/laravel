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

use Symfony\Component\CssSelector\Parser\Token;

/**
 * Represents a "<selector>:<name>(<arguments>)" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class FunctionNode extends AbstractNode
{
    private string $name;

    /**
     * @param Token[] $arguments
     */
    public function __construct(
        private NodeInterface $selector,
        string $name,
        private array $arguments = [],
    ) {
        $this->name = strtolower($name);
    }

    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Token[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }

    public function __toString(): string
    {
        $arguments = implode(', ', array_map(static fn (Token $token) => "'".$token->getValue()."'", $this->arguments));

        return \sprintf('%s[%s:%s(%s)]', $this->getNodeName(), $this->selector, $this->name, $arguments ? '['.$arguments.']' : '');
    }
}
